const content = require('fs').readFileSync(`${__dirname}/../public/index.html`, 'utf8');
const axios = require('axios');
const transactionCache = {};

const httpServer = require('http').createServer((req, res) => {
    if (req.url.startsWith('/update-transactions/')) {
        let accountId = req.url.replace('/update-transactions/', '');
        updateTransactions(accountId)
    }

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

io.of(/^\/transactions\/.*$/).on('connection', socket => {
    let accountId = socket.nsp.name.replace('/transactions/', '');
    console.log('Connection has been established.');

    if (transactionCache.hasOwnProperty(accountId)) {
        console.log('Pushing cached transactions.');
        socket.emit('transaction-update', transactionCache[accountId]);

        return;
    }

    updateTransactions(accountId);
});

httpServer.listen(3000, () => {
    console.log('Socket running on port 3000');
});

function updateTransactions(accountId) {
    console.log(`Updating transactions of "${accountId}"`);

    axios.get(`http://localhost:8000/api/transactions/${accountId}`).then((response) => {
        console.log(`Received ${response.data.length} transactions.`);
        transactionCache[accountId] = response.data;
        emitTransactionUpdate(accountId, response.data);
    }).catch(console.error);
}

function emitTransactionUpdate(accountId, transactions) {
    io.of(`/transactions/${accountId}`).emit('transaction-update', transactions);
}
