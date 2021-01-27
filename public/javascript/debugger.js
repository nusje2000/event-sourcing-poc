$(document).ready(() => {
    $('.js-event-debugger').each((key, value) => {
        const $body = $(value);
        const socket = io(`${$body.data('socket-url')}event-debug`);

        socket.on('connect', () => {
            console.log('Connected to socket.');
        });

        socket.on('incomming-event', (data) => {
            $body.prepend(createEventContainer(data));
        });

        function createEventContainer(data) {
            console.log(data);

            const container = document.createElement('div');
            container.className = 'p-5 bg-blue-200 my-2 mx-5 js-event new';

            const element = document.createElement('div');
            element.className = 'flex';

            const headers = document.createElement('div');
            headers.append(createTitle('Headers:'));
            headers.className = 'flex-1';
            for (const headerName in data.headers) {
                if (data.headers.hasOwnProperty(headerName)) {
                    headers.append(createKeyValuePair(headerName, data.headers[headerName]))
                }
            }
            element.append(headers);

            const body = document.createElement('div');
            body.className = 'flex-1';
            body.append(createTitle('Body:'));
            for (const index in data.payload) {
                if (data.payload.hasOwnProperty(index)) {
                    body.append(createKeyValuePair(index, data.payload[index]));
                }

            }
            element.append(body);
            container.append(element);

            setTimeout(() => {
                $(container).removeClass('new');
            }, 500);

            return container;
        }

        function createTitle(title, tag = 'h3') {
            const element = document.createElement(tag);
            element.className = 'text-lg';
            element.append(document.createTextNode(title));

            return element;
        }

        function createKeyValuePair(name, value) {
            const element = document.createElement('div');
            element.className = 'font-mono';
            const nameField = document.createElement('span');
            nameField.className = 'font-bold';
            nameField.append(document.createTextNode(`${name}: `));
            element.append(nameField);
            element.append(document.createTextNode(`${value}`));

            return element;
        }
    });

    $('.js-event-debugger--close').on('click', () => {
        $('.js-event-debugger').slideUp();
        $('.js-event-debugger--open').show();
        $('.js-event-debugger--close').hide();
    });

    $('.js-event-debugger--open').on('click', () => {
        $('.js-event-debugger').slideDown();
        $('.js-event-debugger--close').show();
        $('.js-event-debugger--open').hide();
    });
});
