const http = require('http');
const fs = require('fs');
const path = require('path');
const { spawn } = require('child_process');
const url = require('url');

const PORT = 3000;
const PUBLIC_DIR = path.join(__dirname, 'public');

const server = http.createServer((req, res) => {
    const parsedUrl = url.parse(req.url, true);
    let pathname = parsedUrl.pathname;
    
    if (pathname.includes('..')) {
        res.writeHead(403);
        res.end('Forbidden');
        return;
    }

    // 🔧 ИСПРАВЛЕНИЕ: ищем index.html вместо index.php
    if (pathname.endsWith('/')) {
        pathname += 'index.html';
    }

    const filePath = path.join(PUBLIC_DIR, pathname);
    const ext = path.extname(filePath).toLowerCase();

    if (ext === '.php') {
        if (!fs.existsSync(filePath)) {
            res.writeHead(404);
            res.end('File not found');
            return;
        }
        const queryString = parsedUrl.search ? parsedUrl.search.substring(1) : '';

        const env = {
            ...process.env,
            REQUEST_METHOD: req.method, // 🔧 Поддержка POST
            QUERY_STRING: queryString,
            SCRIPT_FILENAME: filePath,
            GATEWAY_INTERFACE: 'CGI/1.1',
            REDIRECT_STATUS: '200',
            CONTENT_LENGTH: req.headers['content-length'] || '0',
            CONTENT_TYPE: req.headers['content-type'] || ''
        };

        const phpCommand = 'php-cgi'; 
        const phpArgs = [filePath];

        const php = spawn(phpCommand, phpArgs, { env });

        let output = '';
        let errorOutput = '';

        // 🔧 Передача POST-данных в PHP
        if (req.method === 'POST') {
            req.on('data', chunk => php.stdin.write(chunk));
            req.on('end', () => php.stdin.end());
        }

        php.stdout.on('data', (data) => {
            output += data.toString();
        });

        php.stderr.on('data', (data) => {
            errorOutput += data.toString();
        });

        php.on('close', (code) => {
            if (code !== 0 && !output) {
                res.writeHead(500);
                res.end(`PHP Error: ${errorOutput} (Try installing php-cgi)`);
                return;
            }

            const headerEnd = output.indexOf('\r\n\r\n');
            if (headerEnd !== -1) {
                const headersPart = output.substring(0, headerEnd);
                const bodyPart = output.substring(headerEnd + 4);
                const lines = headersPart.split('\r\n');
                let statusCode = 200;
                let contentType = 'text/html';
                
                lines.forEach(line => {
                    if (line.startsWith('Status:')) {
                        statusCode = parseInt(line.split(' ')[1]);
                    }
                    if (line.startsWith('Content-Type:')) {
                        contentType = line.split(': ')[1];
                    }
                });

                res.writeHead(statusCode, { 'Content-Type': contentType });
                res.end(bodyPart);
            } else {
                res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
                res.end(output);
            }
        });

    } else {
        fs.readFile(filePath, (err, data) => {
            if (err) {
                // 🔧 Более информативная ошибка для отладки
                console.error('File read error:', filePath, err.code);
                res.writeHead(404);
                res.end('File not found: ' + pathname);
                return;
            }

            // 🔧 Правильные MIME-типы для CSS и других файлов
            const mimeTypes = {
                '.css': 'text/css',
                '.js': 'application/javascript',
                '.png': 'image/png',
                '.jpg': 'image/jpeg',
                '.gif': 'image/gif',
                '.svg': 'image/svg+xml',
                '.ico': 'image/x-icon'
            };
            const contentType = mimeTypes[ext] || 'text/html';
            res.writeHead(200, { 'Content-Type': `${contentType}; charset=utf-8` });
            res.end(data);
        });
    }
});

server.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}/`);
    console.log(`Public directory: ${PUBLIC_DIR}`);
    console.log(`Ensure php-cgi is installed for PHP support.`);
});