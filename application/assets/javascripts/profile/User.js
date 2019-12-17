class User {
  constructor(id, firstname, lastname, email, avatar) {
    this.id = id;
    this.firstname = firstname;
    this.lastname = lastname;
    this.email = email;
    this.avatar = avatar;
  }

  getFullName() {
    return this.firstname + ' ' + this.lastname;
  }
}