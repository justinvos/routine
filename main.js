// DEBUG SETUP START
account = 0;
key = "ff5eaf22921ae0fb83917aefb2bb0ac5";
// DEBUG SETUP END

var templates = [];
var tasks = [];

editMode = false;

$("#label_textbox").val("");

loadTemplates();

function findTaskWithId(id) {
  for(i = 0; i < tasks.length; i++) {
    if(tasks[i].id == id) {
      return i;
    }
  }
  return -1;
}

function update() {
  var taskUL = $("#task_list");

  taskUL.empty();

  if(editMode) {
    $("#edit_button li").text("Done");

    for(i = 0; i < tasks.length; i++) {
      var taskIL = $("<li></li>");
      taskIL.addClass("editable");

      if(tasks[i].checked) {
        taskIL.addClass("checked");
      }

      var taskText = $("<input type='text'>");
      taskText.attr("task", tasks[i].id);
      taskText.attr("value", tasks[i].label);
      taskText.addClass("task_textbox");
      taskText.focusout(taskChange);

      taskIL.append(taskText);

      taskUL.append(taskIL);
    }
  } else {
    $("#edit_button li").text("Edit");

    for(i = 0; i < tasks.length; i++) {
      var taskIL = $("<li></li>");
      taskIL.attr("task", tasks[i].id);
      taskIL.text(tasks[i].label);
      taskIL.click(taskClick);

      if(tasks[i].checked) {
        taskIL.addClass("checked");
      }

      taskUL.append(taskIL);
    }
  }
}




$(document).on("click", "#template_list li" , function(element) {
  loadTemplate(element.target.getAttribute("template"));
});


function taskClick(element) {
  if(element.target.nodeName == "INPUT") {
    return;
  }

  var taskIL = $(element.target);

  tasks[findTaskWithId(taskIL.attr("task"))].checked = !tasks[findTaskWithId(taskIL.attr("task"))].checked;

  taskIL.toggleClass("checked");
}



$("#edit_button").click(function(element) {

  editMode = !editMode;

  console.log(editMode);

  update();
});


function taskChange(element) {

  tasks[findTaskWithId(element.target.getAttribute("task"))].label = element.target.value;

  $.ajax({
    url: "api/task.php",
    method: "PUT",
    data: {
      "account": account,
      "key": key,
      "id": element.target.getAttribute("task"),
      "label": element.target.value
    },
    dataType: "json",
    success: function(result) {
      if(result.error) {
        console.log(result.error_msg);
      }
    }
  });
}


function loadTemplates() {
  $.ajax({
    url: "api/template.php",
    method: "GET",
    data: {
      "account": account,
      "key": key
    },
    dataType: "json",
    success: function(result) {
      var templateList = document.getElementById("template_list");
      $("#template_list").empty();

      tasks = [];

      for(i = 0; i < result.templates.length; i++) {
        var template = document.createElement("li");
        template.innerHTML = result["templates"][i]["label"];
        template.setAttribute("template", result["templates"][i]["id"]);
        templateList.appendChild(template);

        tasks.push(result["templates"][i]["id"]);
      }
    }
  });
}

function loadTemplate(template) {
  $.ajax({
    url: "api/template.php",
    method: "GET",
    data: {
      "account": account,
      "key": key,
      "id": template
    },
    dataType: "json",
    success: function(result) {
      $("#label_textbox").val(result.label);

      $.ajax({
        url: "api/task.php",
        method: "GET",
        data: {
          "account": account,
          "key": key,
          "template": template
        },
        dataType: "json",
        success: function(result) {
          tasks = [];
          for(i = 0; i < result["tasks"].length; i++) {
            addTask(result["tasks"][i]["id"], result["tasks"][i]["label"]);
          }

          update();
        }
      });
    }
  });
}

function addTask(id, label) {
  tasks.push({
    "id": id,
    "label": label,
    "checked": false
  });
}
