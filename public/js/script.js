$('#submitUserData').on('click', function () {
  let userData = {
    name: $('#userName').val(),
  };

  $.ajax({
    url: '/user',
    method: "POST",
    data: userData
  })
    .done(function () {
      $('#register').addClass('visually-hidden');

      let userContainer = $('#registeredUser');

    });
  console.log(userData);
});