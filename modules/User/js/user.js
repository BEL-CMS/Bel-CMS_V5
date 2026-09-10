document.addEventListener('DOMContentLoaded', () => {

    document
        .querySelectorAll('.user-register-password-toggle')
        .forEach(button => {

            button.addEventListener('click', () => {

                const targetId = button.dataset.target;

                if (!targetId) {
                    return;
                }

                const input =
                    document.getElementById(targetId);

                if (!input) {
                    return;
                }

                const icon =
                    button.querySelector('i');

                if (input.type === 'password') {

                    input.type = 'text';

                    if (icon) {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }

                    button.setAttribute(
                        'aria-label',
                        'Masquer le mot de passe'
                    );

                } else {

                    input.type = 'password';

                    if (icon) {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }

                    button.setAttribute(
                        'aria-label',
                        'Afficher le mot de passe'
                    );
                }
            });

        });

});