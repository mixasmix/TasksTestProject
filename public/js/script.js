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
});

window.onload = function () {
  $.ajax({
    method: 'GET',
    url: '/task',
  })
    .done(function (serverData) {
      let taskCardContainer = $('#task-cards div.row');
      serverData.data.map(function (task) {
        taskCardContainer.append(
          '<div class="feature col">\n' +
          '    <div class="feature-icon d-inline-flex align-items-center justify-content-center bg-primary bg-gradient text-white fs-2 mb-3">\n' +
          '        <svg class="bi" width="1em" height="1em"><use xlink:href="#collection"/></svg>\n' +
          '    </div>\n' +
          '    <h2>' + task.name + '</h2>\n' +
          '    <p>Автор: ' + task.author.name + '</p>\n' +
          '    <p class="currentStatus">Текущий статус: <span>' + task.status + '</span></p>\n' +
          '    <p class="currentPriority">Текущий приоритет: <span>' + task.priority + '</span></p>\n' +
          '    <p class="currentTags">Теги: <span></span></p>\n' +
          '    <label class="form-label">Приоритет</label>' +
          '    <select name="priority" id="priority' + task.id + '">' +
          '       <option value="low">Низкий</option>' +
          '       <option value="medium">Средний</option>' +
          '       <option value="high">Высокий</option>' +
          '    </select><hr>' +
          '    <label class="form-label">Статус</label>' +
          '    <select name="status" id="status' + task.id + '">' +
          '       <option value="work">В работе</option>' +
          '       <option value="cancelled">Завершена</option>' +
          '    </select>' +
          '</div>'
        );

        let tagArea = taskCardContainer.find('p.currentTags span');
        console.log(task)
        let tags = task.tags.map(function (tag) {
          console.log(tag.name)
          tagArea.append(
            '<a href="#">' + tag.name + ' <a href="#"  data-tag-name="' + tag.name + '" data-task-id="' + task.id + '" class="btn-close"></a></a> , '
          );
        })
      });
    });
};

let body = $('body');

body.delegate("select[name=priority]", "change", function () {
  let select = $(this);

  $.ajax({
    url: '/task/' + select.attr('id').replace('priority', '') + '/priority/' + select.val(),
    method: 'PUT',
  })
    .done(function (serverData) {
      select.parent().find('p.currentPriority span').text(serverData.data.priority)
    });
});

body.delegate("select[name=status]", "change", function () {
  let select = $(this);

  $.ajax({
    url: '/task/' + select.attr('id').replace('status', '') + '/status/' + select.val(),
    method: 'PUT',
  })
    .done(function (serverData) {
      select.parent().find('p.currentStatus span').text(serverData.data.status)
    });
});

body.delegate('a.btn-close', 'click', function () {
  let link = $(this);

  $.ajax({
    url: '/task/' + link.attr('data-task-id') + '/tag/' + link.attr('data-tag-name'),
    method: 'DELETE',
  });
  link.parent().remove();
})