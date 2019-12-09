class User {
  constructor(id, firstname, lastname, email) {
    this.id = id;
    this.firstname = firstname;
    this.lastname = lastname;
    this.email = email;
  }

  getFullName() {
    return this.firstname + ' ' + this.lastname;
  }
}