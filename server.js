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

    if (pathname.endsWith('/')) {
        pathname += 'index.php';
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
            REQUEST_METHOD: 'GET',
            QUERY_STRING: queryString,
            SCRIPT_FILENAME: filePath,
            GATEWAY_INTERFACE: 'CGI/1.1',
            REDIRECT_STATUS: '200'
        };

        const phpCommand = 'php-cgi'; 
        const phpArgs = [filePath];

        const php = spawn(phpCommand, phpArgs, { env });

        let output = '';
        let errorOutput = '';

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
                res.writeHead(404);
                res.end('File not found');
                return;
            }

            const contentType = ext === '.css' ? 'text/css' : 'text/html';
            res.writeHead(200, { 'Content-Type': `${contentType}; charset=utf-8` });
            res.end(data);
        });
    }
});

server.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}/`);
    console.log(`Ensure php-cgi is installed for proper $_GET support.`);
});