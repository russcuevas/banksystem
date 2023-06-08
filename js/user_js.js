// PROFILE TOGGLE BUTTON
const profileContainer = document.querySelector('.profile');
const dropdown = document.querySelector('.dropdown');

let isOpen = false;

profileContainer.addEventListener('click', () => {
  isOpen = !isOpen;
  if (isOpen) {
    dropdown.style.display = 'block';
  } else {
    dropdown.style.display = 'none';
  }
});

document.addEventListener('click', (event) => {
  const targetElement = event.target;
  if (!profileContainer.contains(targetElement)) {
    isOpen = false;
    dropdown.style.display = 'none'; 
  }
});