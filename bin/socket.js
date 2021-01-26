const content = require('fs').readFileSync(`${__dirname}/../public/index.html`, 'utf8');

const httpServer = require('http').createServer((req, res) => {
    // serve the index.html file
    res.setHeader('Content-Type', 'text/html');
    res.setHeader('Content-Length', Buffer.byteLength(content));
    res.end(content);
});

const io = require('socket.io')(httpServer, {
    cors: {
        origin: "http://localhost:8000",
        methods: ["GET", "POST"]
    }
});

io.on('connection', socket => {
    console.log('connect');
});

httpServer.listen(3000, () => {
    console.log('go to http://localhost:3000');
});
