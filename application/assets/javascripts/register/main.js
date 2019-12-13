if(localStorage.getItem('id') || localStorage.getItem('access_token')) {
  location.href = '../users/profile';
}

const registerButton = document.querySelector('#reg-button');

registerButton.addEventListener('click', () => {
  const emailField = document.querySelector('#email-field').value;
  const firstnameField = document.querySelector('#firstname-field').value;
  const lastnameField = document.querySelector('#lastname-field').value;
  const passwordField = document.querySelector('#password-field').value;

  if(firstnameField.length < 1 || firstnameField.length > 32) {
    const mb = [
      { text: 'OK', click() { this.hide() } },
    ];

    const errModal = new Modal('Имя должно содержать от 1 до 32 букв.', mb);
    errModal.show();
  } else if(lastnameField.length < 1 || lastnameField.length > 32) {
    const mb = [
      { text: 'OK', click() { this.hide() } },
    ];

    const errModal = new Modal('Фамилия должна содержать от 1 до 32 букв.', mb);
    errModal.show();
  } else if(passwordField.length < 6) {
    const mb = [
      { text: 'OK', click() { this.hide() } },
    ];

    const errModal = new Modal('Пароль должен быть не короче 6 символов.', mb);
    errModal.show();
  } else {
    const registerRequestUrl = '../api/users.register';
    const registerRequestBody = {
      email: emailField,
      firstname: firstnameField,
      lastname: lastnameField,
      password: passwordField
    }

    sendRequest('POST', registerRequestUrl, registerRequestBody)
      .then(data => {
        if(data.data.response) {
          const mb = [
            { text: 'OK', click: () => { location.href = '../users/login' } },
          ];
      
          const okModal = new Modal('Успешная регистрация!', mb);
          okModal.show();
        } else if(data.data.error.error_code == errorCodes.EMAIL_ALREADY_EXISTS) {
          const mb = [
            { text: 'OK', click() { this.hide() } },
          ];
      
          const errModal = new Modal('Пользователь с таким Email уже зарегистрирован.', mb);
          errModal.show();
        }
      })
      .catch(err => {
        const mb = [
          { text: 'OK', click() { this.hide() } },
        ];
  
        const errModal = new Modal('Ошибка доступа.', mb);
        errModal.show();
      });
  }
});