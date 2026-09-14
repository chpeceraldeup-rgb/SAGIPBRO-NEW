(() => {
    const inbox = document.querySelector('[data-messages-inbox]');
    if (!inbox) return;
    const status = inbox.querySelector('[data-messages-live-status]');
    const body = inbox.querySelector('tbody');
    const count = inbox.querySelector('.data-card-header > .status-badge');
    let timer;
    let busy = false;
    let stopped = false;
    let lastData = '';

    function render(messages) {
        const signature = JSON.stringify(messages);
        if (signature === lastData) return;
        const rows = document.createDocumentFragment();
        const element = (tag, text, className = '') => {
            const node = document.createElement(tag);
            node.textContent = text;
            node.className = className;
            return node;
        };
        for (const message of messages) {
            const row = document.createElement('tr');
            const sender = document.createElement('td');
            sender.append(element('span', message.name, 'table-primary-text'), element('span', message.email, 'table-secondary-text'));
            row.append(sender);
            for (const value of [message.sitio || 'Not provided', message.phone || 'Not provided', message.subject]) {
                row.append(element('td', value));
            }
            const content = element('td', message.message);
            content.style.maxWidth = '420px';
            content.style.whiteSpace = 'pre-line';
            row.append(content, element('td', message.created_at));
            const state = document.createElement('td');
            state.append(element('span', message.status, 'status-badge ' + (message.status === 'Unread' ? 'status-warning' : 'status-neutral')));
            row.append(state);
            rows.append(row);
        }
        if (!messages.length) {
            const row = document.createElement('tr');
            const empty = element('td', 'No messages received yet.', 'text-center text-body-secondary py-4');
            empty.colSpan = 7;
            row.append(empty);
            rows.append(row);
        }
        body.replaceChildren(rows);
        count.textContent = `${messages.length} messages`;
        lastData = signature;
    }

    async function refresh() {
        clearTimeout(timer);
        if (busy || stopped || document.hidden) return;
        busy = true;
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 10000);
        try {
            const response = await fetch('../../api/messages.php', {
                credentials: 'same-origin', cache: 'no-store', signal: controller.signal
            });
            if (response.redirected || response.status === 401 || response.status === 403) {
                stopped = true;
                throw new Error('Please sign in again to receive new messages.');
            }
            if (!response.ok) throw new Error();
            const payload = await response.json();
            if (!Array.isArray(payload.data)) throw new Error();
            render(payload.data);
            status.textContent = 'Messages update automatically.';
        } catch (error) {
            status.textContent = stopped ? error.message : 'Unable to check new messages. Reconnecting automatically…';
        } finally {
            clearTimeout(timeout);
            busy = false;
            if (!stopped && !document.hidden) timer = setTimeout(refresh, 1000);
        }
    }
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) clearTimeout(timer);
        else refresh();
    });
    window.addEventListener('online', refresh);
    refresh();
})();
