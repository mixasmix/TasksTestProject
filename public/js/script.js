$('#submitUserData').on('click', function () {
  let userData = {
    name: $('#userName').val(),
  };

  $.ajax({
    url: '/user',
    method: "POST",
    data: JSON.stringify(userData)
  })
    .done(function (serverData) {
      let data = serverData.data;

      $('#register').addClass('visually-hidden');

      let userContainer = $('#registeredUser');

      userContainer.find('#userNameParagraph').text(data.name);
      userContainer.find('#userRegisterDate').text(data.created_at);
      userContainer.find('#userId').text(data.id);

      userContainer.removeClass('visually-hidden');
    });
  console.log(userData);
});