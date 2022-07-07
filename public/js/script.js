$('#submitUserData').on('click', function () {
  this.preventDefault;
  let userData = {
    "name": $('#userName').val(),
  };
  console.log(userData);
});