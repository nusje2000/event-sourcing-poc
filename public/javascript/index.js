$(document).ready(() => {
    $('.js-transactions').each((key, value) => {
        const CLASSES = {
            SUCCESS_ROW: 'bg-green-100 text-green-900',
            FAILURE_ROW: 'bg-red-100 text-red-900',
        };

        const $body = $(value);
        const socket = io(`${$body.data('socket-url')}transactions/${$body.data('account-id')}`);

        socket.on('connect', () => {
            console.log('Connected to socket.');
        });

        socket.on('transaction-update', (data) => {
            if (data.length > 0) {
                $body.find('.js-information-row').hide();
                displayTransactions($body, data);

                return;
            }

            $body.find('.js-information-row').text('No transactions found.');
            $body.find('.js-information-row').show();
        });

        function displayTransactions($body, transactions) {
            $body.find('.data-row').remove();

            for (const transaction of transactions) {
                const row = document.createElement('tr');
                row.append(createColumn([document.createTextNode(transaction.id)]));
                row.append(createCurrencyColumn(transaction.amount, transaction.success));
                row.append(createSuccessStatusRow(transaction.success));
                row.append(createColumn([document.createTextNode(transaction.timestamp)]));
                $body.append(row);
            }
        }

        function createColumn(contents, classes) {
            classes = classes || [];

            const element = document.createElement('td');
            element.className = `data-row p-1 border border-blue-800 ${classes.join(' ')}`;
            for (let content of contents) {
                element.append(content);
            }

            return element;
        }

        function createCurrencyColumn(amount, success) {
            const classes = ['font-bold'];

            if (!success) {
                classes.push('line-through')
            }

            if (amount > 0) {
                classes.push(CLASSES.SUCCESS_ROW)
            }

            if (amount < 0) {
                classes.push(CLASSES.FAILURE_ROW)
            }

            return createColumn([document.createTextNode(formatCurrency(amount))], classes);
        }

        function createSuccessStatusRow(success) {
            const classes = ['font-bold'];
            success ? classes.push(CLASSES.SUCCESS_ROW) : classes.push(CLASSES.FAILURE_ROW);

            return createColumn([document.createTextNode(success ? 'Accepted' : 'Denied')], classes);
        }

        function formatCurrency(amount) {
            const formatted = `${(Math.abs(amount) / 100).toFixed(2)} euro`;

            if (amount > 0) {
                return `+ ${formatted}`;
            }

            if (amount < 0) {
                return `- ${formatted}`;
            }

            return formatted;
        }
    });
});
