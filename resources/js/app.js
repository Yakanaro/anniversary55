const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

/**
 * Lay a ring of bulbs around the marquee sign and make them chase.
 */
function initMarquee() {
    const sign = document.querySelector('[data-marquee]');

    if (!sign) {
        return;
    }

    const holder = sign.querySelector('[data-bulbs]');
    let bulbs = [];

    const placeBulbs = () => {
        holder.innerHTML = '';
        bulbs = [];

        const width = sign.offsetWidth;
        const height = sign.offsetHeight;
        const inset = 12;
        const innerWidth = width - 2 * inset;
        const innerHeight = height - 2 * inset;
        const perimeter = 2 * (innerWidth + innerHeight);
        const count = Math.floor(perimeter / 28);

        for (let index = 0; index < count; index++) {
            const distance = (index * perimeter) / count;
            let x;
            let y;

            if (distance < innerWidth) {
                x = inset + distance;
                y = inset;
            } else if (distance < innerWidth + innerHeight) {
                x = width - inset;
                y = inset + distance - innerWidth;
            } else if (distance < 2 * innerWidth + innerHeight) {
                x = width - inset - (distance - innerWidth - innerHeight);
                y = height - inset;
            } else {
                x = inset;
                y = height - inset - (distance - 2 * innerWidth - innerHeight);
            }

            const bulb = document.createElement('i');
            bulb.style.left = `${x}px`;
            bulb.style.top = `${y}px`;
            holder.appendChild(bulb);
            bulbs.push(bulb);
        }
    };

    let step = 0;
    const chase = () => {
        bulbs.forEach((bulb, index) => bulb.classList.toggle('on', (index + step) % 3 !== 0));
        step++;
    };

    placeBulbs();
    addEventListener('resize', placeBulbs);
    chase();

    if (!reduceMotion) {
        setInterval(chase, 180);
    }
}

/**
 * Count the film leader down from 3 once the curtain has opened.
 */
function initFilmLeader() {
    const number = document.querySelector('[data-leader-number]');

    if (!number || reduceMotion) {
        return;
    }

    let remaining = 3;

    setTimeout(() => {
        const timer = setInterval(() => {
            remaining--;

            if (remaining < 1) {
                clearInterval(timer);

                return;
            }

            number.textContent = remaining;
        }, 500);
    }, 2200);
}

/**
 * Show the time left until the celebration starts.
 */
function initCountdown() {
    const output = document.querySelector('[data-countdown]');

    if (!output) {
        return;
    }

    const startsAt = new Date(output.dataset.countdown).getTime();
    const pad = (value) => String(value).padStart(2, '0');

    const tick = () => {
        let left = Math.max(0, startsAt - Date.now());
        const days = Math.floor(left / 864e5);
        left %= 864e5;
        const hours = Math.floor(left / 36e5);
        left %= 36e5;
        const minutes = Math.floor(left / 6e4);
        const seconds = Math.floor((left % 6e4) / 1e3);

        output.textContent = `${days} дн. ${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
    };

    tick();
    setInterval(tick, 1000);
}

/**
 * Submit the RSVP without leaving the page and tear off the ticket stub.
 */
function initTicket() {
    const ticket = document.querySelector('[data-ticket]');

    if (!ticket) {
        return;
    }

    const form = ticket.querySelector('form');
    const issued = ticket.querySelector('[data-issued]');
    const error = form.querySelector('[data-error]');
    const submit = form.querySelector('button[type="submit"]');
    const fill = (name, value) => {
        ticket.querySelector(`[data-ticket-${name}]`).textContent = value;
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const data = new FormData(form);

        if (!data.get('first_name')?.trim() || !data.get('last_name')?.trim() || !data.get('drink')) {
            error.textContent = 'Чтобы выдать билет, заполните все три поля.';

            return;
        }

        error.textContent = '';
        submit.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: data,
            });
            const body = await response.json().catch(() => ({}));

            if (response.status === 422) {
                error.textContent = Object.values(body.errors ?? {})[0]?.[0] ?? 'Проверьте поля формы.';

                return;
            }

            if (!response.ok) {
                error.textContent = response.status === 429
                    ? 'Слишком много попыток. Подождите минуту и попробуйте снова.'
                    : 'Не получилось отправить. Обновите страницу и попробуйте ещё раз.';

                return;
            }

            fill('name', body.ticket.name);
            fill('number', `№ ${body.ticket.number}`);
            fill('row', body.ticket.row);
            fill('seat', body.ticket.seat);
            fill('drink', body.ticket.drink);
            form.hidden = true;
            issued.hidden = false;
            ticket.classList.add('torn');
        } catch {
            error.textContent = 'Нет связи с сервером. Проверьте интернет и попробуйте ещё раз.';
        } finally {
            submit.disabled = false;
        }
    });

    ticket.querySelector('[data-again]').addEventListener('click', (event) => {
        event.preventDefault();
        form.reset();
        issued.hidden = true;
        form.hidden = false;
        ticket.classList.remove('torn');
        fill('number', 'Петрозаводск');
        form.querySelector('input').focus();
    });
}

initMarquee();
initFilmLeader();
initCountdown();
initTicket();
