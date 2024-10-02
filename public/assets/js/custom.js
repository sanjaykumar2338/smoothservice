$(document).ready(function() {
    $('#parent_services').select2();
    $('#select_team').select2();
    $('#order_team_member').select2();
    $('#onboarding_field').select2();
    $('#country').select2();
    $('#language').select2();
    $('#timeZones').select2();
    $('#currency').select2();
});

// Update/reset user image of account page
let accountUserImage = document.getElementById('uploadedAvatar');
const fileInput = document.querySelector('.account-file-input');

if (accountUserImage) {
  const resetImage = accountUserImage.src;
  fileInput.onchange = () => {
    if (fileInput.files[0]) {
      accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
    }
  };
}