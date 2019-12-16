function buildNavMenu(id) {
  const navContent = document.querySelector('.nav__list');

  const navProfile = navContent.querySelector('#nav-profile');
  navProfile.onclick = () => {
    location.href = '../users/profile?id=' + id;
  }

  const navLogout = navContent.querySelector('#nav-logout');
  navLogout.onclick = () => {
    localStorage.clear();
    location.reload();
  }

  const navFollowers = navContent.querySelector('#nav-followers');
  navFollowers.onclick = () => {
    location.href = '../followers/followers?id=' + id;
  }
}