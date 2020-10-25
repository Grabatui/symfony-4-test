import 'jquery';
import 'popper.js';
import 'bootstrap';
import 'holderjs';

const notificationsCount = () => {
    if (!window.IS_USER) {
        return;
    }

    const countElement = document.getElementById(`notifications-count`);

    const fetchNotificationsCount = () => {
        fetch(window.PATHS.unread_notifications, {credentials: `include`})
            .then((response) => response.json())
            .then((response) => {
                countElement.innerText = response.count;

                setTimeout(fetchNotificationsCount, 3000);
            })
            .catch((reason) => {
                console.error(reason);
            });
    };

    fetchNotificationsCount();
};

document.addEventListener(`DOMContentLoaded`, () => {
    notificationsCount();
});
