class Modal {
  constructor(text, buttons = []) {
    const buttonElements = buttons.map(({ text, click }) => {
      return createElement('button', {
        class: 'modal__button',
        on: {
          click: click && click.bind(this)
        }
      }, text);
    });

    this.buttons = buttonElements;

    this.modal = createElement('div', {
      class: 'modals__wrapper',
    }, [
      createElement('div', {
        class: 'modal',
      }, [
        createElement('p', {
          class: 'modal__text',
        }, text),
        ...buttonElements
      ]),
    ]);

    this.modalWrapper = document.querySelector('.modals');
  }

  show() {
    this.modalWrapper.append(this.modal);
  }

  hide() {
    this.modal.remove();
  }

}
