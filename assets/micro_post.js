const likeButtons = () => {
    const likeButton = document.getElementById(`like`);
    const unlikeButton = document.getElementById(`unlike`);

    const switchButtons = (button, oppositeButton) => {
        button.style.display = `none`;
        oppositeButton.style.display = `block`;
    };

    const addOnClickButtonEvent = (button, oppositeButton, path) => {
        const callback = (event) => {
            if (!window.USER_ID) {
                return window.location.href = window.PATHS.login;
            }

            event.preventDefault();

            button.disabled = true;

            fetch(path, {credentials: `include`})
                .then((response) => response.json())
                .then((response) => {
                    oppositeButton.querySelector(`.js-likes_count`).innerText = response.count;

                    switchButtons(button, oppositeButton);

                    button.disabled = false;
                })
                .catch(() => {
                    switchButtons(button, oppositeButton);

                    button.disabled = false;
                });
        };

        button.removeEventListener(`click`, callback);
        button.addEventListener(`click`, callback);
    };

    addOnClickButtonEvent(likeButton, unlikeButton, window.PATHS.like);
    addOnClickButtonEvent(unlikeButton, likeButton, window.PATHS.unlike);
};

document.addEventListener(`DOMContentLoaded`, () => {
    likeButtons();
});
