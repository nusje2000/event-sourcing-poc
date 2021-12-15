const axios = require('axios');
const transactionCache = {};
const debugEvents = [];

const httpServer = require('http').createServer((req, res) => {
    if (req.url.startsWith('/update-transactions/')) {
        handleTransactionUpdateRequest(req);
    }

    if (req.url === '/debug-event') {
        handleEventDebug(req);
    }

    const content = 'OK';
    res.setHeader('Content-Type', 'text/html');
    res.setHeader('Content-Length', Buffer.byteLength(content));
    res.end(content);
});

const io = require('socket.io')(httpServer, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});

io.of('/event-debug').on('connection', socket => {
    console.log('[DEBG] Debug session is connected.');

    for (const event of debugEvents) {
        socket.emit('incomming-event', event);
    }
});

io.of(/^\/transactions\/.*$/).on('connection', socket => {
    let accountId = socket.nsp.name.replace('/transactions/', '');
    console.log('[CONN] Connection has been established.');

    if (transactionCache.hasOwnProperty(accountId)) {
        console.log('[DATA] Pushing cached transactions.');
        socket.emit('transaction-update', transactionCache[accountId]);

        return;
    }

    fetchTransaction(accountId);
});

httpServer.listen(3000, () => {
    console.log('[CONN] Socket running on port 3000');
});

function handleTransactionUpdateRequest(req) {
    let accountId = req.url.replace('/update-transactions/', '');

    var body = '';

    req.on('readable', function () {
        body += req.read() || '';
    });

    req.on('end', function () {
        updateTransaction(accountId, JSON.parse(body));
    });
}

function handleEventDebug(req) {
    var body = '';

    req.on('readable', function () {
        body += req.read() || '';
    });

    req.on('end', function () {
        console.log('[DEBG] Received event.');
        addDebugEvent(JSON.parse(body));
    });
}

function addDebugEvent(event) {
    debugEvents.push(event);
    io.of(`/event-debug`).emit('incomming-event', event);

    while (debugEvents.length > 10) {
        debugEvents.shift();
    }
}

function updateTransaction(accountId, transactions) {
    console.info(`[INFO] Received ${transactions.length} transactions for "${accountId}".`);
    transactionCache[accountId] = transactions;
    io.of(`/transactions/${accountId}`).emit('transaction-update', transactions);
}

function fetchTransaction(accountId) {
    axios.get(`http://nginx/api/transactions/${accountId}`).then((response) => {
        updateTransaction(accountId, response.data);
    }).catch(console.error);
}
