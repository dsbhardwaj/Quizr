document.querySelectorAll('.dropbtn').forEach(button => {
  button.addEventListener('click', function() {
    const dropdown = this.nextElementSibling;
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
  });
});
