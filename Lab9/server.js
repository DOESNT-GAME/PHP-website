const http = require('http');
const fs = require('fs');
const path = require('path');
const { spawn } = require('child_process');
const url = require('url');

const PORT = 3000;
const PUBLIC_DIR = path.join(__dirname, 'public');

function getSafeFilePath(baseDir, pathname) {
    // Убираем ведущий и конечные слеши
    let cleanPath = pathname.replace(/^\/+|\/+$/g, '');
    // Если путь пустой (корень сайта) — возвращаем index.php
    if (cleanPath === '') cleanPath = 'index.php';
    const fullPath = path.join(baseDir, cleanPath);
    // Защита от выхода за пределы PUBLIC_DIR
    if (!fullPath.startsWith(path.resolve(baseDir) + path.sep) && fullPath !== path.resolve(baseDir, 'index.php')) {
        return null;
    }
    return fullPath;
}

const server = http.createServer((req, res) => {
    const parsedUrl = url.parse(req.url, true);
    let pathname = parsedUrl.pathname;
    
    const filePath = getSafeFilePath(PUBLIC_DIR, pathname);
    
    if (!filePath) {
        res.writeHead(403);
        res.end('Forbidden: Invalid path');
        return;
    }

    const ext = path.extname(filePath).toLowerCase();

    if (ext === '.php') {
        if (!fs.existsSync(filePath)) {
            console.error('PHP file not found:', filePath);
            res.writeHead(404);
            res.end(`File not found: ${pathname}`);
            return;
        }

        const queryString = parsedUrl.search ? parsedUrl.search.substring(1) : '';
        
        // Автоопределение PHP-команды
        const phpCommands = ['php-cgi', 'php', 'php8.2', 'php8.1', 'php8.0', 'php7.4'];
        let phpCommand = null;
        for (const cmd of phpCommands) {
            try {
                require('child_process').execSync(cmd + ' -v', { stdio: 'ignore' });
                phpCommand = cmd;
                break;
            } catch (e) {}
        }
        
        if (!phpCommand) {
            res.writeHead(500);
            res.end('PHP interpreter not found. Install php-cgi or php-cli.');
            return;
        }

        const env = {
            ...process.env,
            REQUEST_METHOD: req.method,
            QUERY_STRING: queryString,
            SCRIPT_FILENAME: filePath,
            DOCUMENT_ROOT: PUBLIC_DIR,
            GATEWAY_INTERFACE: 'CGI/1.1',
            REDIRECT_STATUS: '200',
            CONTENT_LENGTH: req.headers['content-length'] || '0',
            CONTENT_TYPE: req.headers['content-type'] || ''
        };

        const phpArgs = phpCommand === 'php-cgi' ? [filePath] : ['-f', filePath];
        const php = spawn(phpCommand, phpArgs, { env, cwd: PUBLIC_DIR });

        let output = '';
        let errorOutput = '';

        if (req.method === 'POST') {
            req.on('data', chunk => php.stdin.write(chunk));
            req.on('end', () => php.stdin.end());
        } else {
            php.stdin.end();
        }

        php.stdout.on('data', data => { output += data.toString(); });
        php.stderr.on('data', data => { errorOutput += data.toString(); });

        php.on('close', code => {
            if (code !== 0 && !output) {
                console.error('PHP error:', errorOutput);
                res.writeHead(500);
                res.end(`PHP Error: ${errorOutput}`);
                return;
            }
            const headerEnd = output.indexOf('\r\n\r\n');
            if (headerEnd !== -1) {
                const headersPart = output.substring(0, headerEnd);
                const bodyPart = output.substring(headerEnd + 4);
                const lines = headersPart.split('\r\n');
                let statusCode = 200;
                let contentType = 'text/html; charset=utf-8';
                const headers = {};
                for (const line of lines) {
                    if (line.toLowerCase().startsWith('status:')) {
                        statusCode = parseInt(line.split(' ')[1]) || 200;
                    } else if (line.toLowerCase().startsWith('content-type:')) {
                        contentType = line.substring(line.indexOf(':') + 1).trim();
                    } else if (line.includes(':')) {
                        const [k, v] = line.split(':');
                        headers[k.trim()] = v.trim();
                    }
                }
                headers['Content-Type'] = contentType;
                res.writeHead(statusCode, headers);
                res.end(bodyPart);
            } else {
                res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
                res.end(output);
            }
        });

        php.on('error', err => {
            console.error('PHP spawn error:', err.message);
            res.writeHead(500);
            res.end(`Failed to execute PHP: ${err.message}`);
        });

    } else {
        // Статические файлы
        fs.readFile(filePath, (err, data) => {
            if (err) {
                if (err.code === 'ENOENT') {
                    console.warn('File not found:', filePath);
                    res.writeHead(404);
                    res.end(`File not found: ${pathname}`);
                } else {
                    console.error('Read error:', err);
                    res.writeHead(500);
                    res.end('Internal Server Error');
                }
                return;
            }
            const mimeTypes = {
                '.css': 'text/css', '.js': 'application/javascript',
                '.png': 'image/png', '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg',
                '.gif': 'image/gif', '.svg': 'image/svg+xml', '.ico': 'image/x-icon'
            };
            const contentType = mimeTypes[ext] || 'application/octet-stream';
            const charset = ['.html', '.css', '.js', '.txt', '.php'].includes(ext) ? '; charset=utf-8' : '';
            res.writeHead(200, { 'Content-Type': `${contentType}${charset}` });
            res.end(data);
        });
    }
});

server.listen(PORT, () => {
    console.log(`✅ Server running at http://localhost:${PORT}/`);
    console.log(`📁 Public: ${PUBLIC_DIR}`);
});