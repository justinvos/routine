if(typeof(Storage) === "undefined") {
  console.log("Web storage is not supported")
}

if(localStorage.getItem("projects") == null) {
  console.log("Projects initialisation");
  localStorage.setItem("projects", JSON.stringify([]));
}

if(localStorage.getItem("templates") == null) {
  console.log("Templates initialisation");
  localStorage.setItem("templates", JSON.stringify([]));
}

var projects = JSON.parse(localStorage.getItem("projects"));
var templates = JSON.parse(localStorage.getItem("templates"));


function flush() {
  localStorage.setItem("projects", JSON.stringify(projects));
  localStorage.setItem("templates", JSON.stringify(templates));
}

function addProject() {
  var newProject = {
    label:"",
    stage:0,
    tasks:[]
  };
  projects.push(newProject);
  return projects.indexOf(newProject);
}

function addTemplate() {
  var newTemplate = {
    label:"",
    tasks:[]
  };
  templates.push(newTemplate);
  return templates.indexOf(newTemplate);
}

function addTaskToProject(projectId) {
  var newTask = {
    completed:false,
    label:"",
    prereq:null,
    stage:0
  };
  projects[projectId].tasks.push(newTask);
  return projects[projectId].tasks.indexOf(newTask);
}

function addTaskToTemplate(templateId) {
  var newTask = {
    completed:false,
    label:"",
    prereq:null,
    stage:0
  };
  templates[templateId].tasks.push(newTask);
  return templates[templateId].tasks.indexOf(newTask);
}


function createProjectFromTemplate(templateId) {
  var newProjectId = addProject();
  projects[newProjectId].label = templates[templateId].label
  projects[newProjectId].tasks = templates[templateId].tasks
  return newProjectId;
}



// ### LOCAL DATA FUNCTIONS ###

function addTask() {
  var newTask = {
    completed:false,
    label:"",
    prereq:null,
    stage:0
  };
  tasks.push(newTask);
  return tasks.indexOf(newTask);
}

function changePrereq(taskId, prereq) {
  if(tasks[prereq] !== null && tasks[prereq].stage === tasks[taskId].stage) {
    tasks[taskId].prereq = prereq;
  }
}

function changeStage(taskId, stage) {
  tasks[taskId].stage = stage;
  tasks[taskId].prereq = null;

  for(var i = 0; i < tasks.length; i++) {
    if(tasks[i] !== null) {
      if(tasks[i].prereq === taskId) {
        changeStage(i, stage);
      }
    }
  }
}

function isProjectTaskCompleted(taskId) {
  if(tasks[taskId].completed === true){
    return true;
  } else {
    return false;
  }
}

function isProjectTaskNext(taskId) {
  if(isProjectTaskCompleted(taskId)) {
    return false;
  }

  if(tasks[taskId].stage !== viewStage) {
    return false;
  }

  if(tasks[taskId].prereq === null) {
    return true;
  } else if(tasks[tasks[taskId].prereq].completed) {
    return true;
  } else {
    return false;
  }
}

// ### USER INTERFACE FUNCTIONS ###

var currentProject = 0;

var viewLabel = null;
var viewStage = null;
var tasks = null;


var taskFilter = 2; // 0 = no filter, 1 = all uncompleted tasks, 2 = next tasks, 3 = completed


// EVENT BINDINGS
$("#newTask_link").click(newTaskClick);

$("#showAll_link").click(function(){
  taskFilter = 0;
  refresh();
});


$("#showUncompleted_link").click(function(){
  taskFilter = 1;
  refresh();
});

$("#showNext_link").click(function(){
  taskFilter = 2;
  refresh();
});

$("#showCompleted_link").click(function(){
  taskFilter = 3;
  refresh();
});


function refresh() {

  $("#project_label").text(viewLabel);

  $("#task_list").empty();

  if(taskFilter === 0) {
    printAllProjectTasks();
  } else if(taskFilter === 1) {
    printUncompletedProjectTasks();
  } else if(taskFilter === 2) {
    printNextProjectTasks();
  } else if(taskFilter === 3) {
    printCompletedProjectTasks();
  }
}




function printProjectTask(id) {
  var newLI = $("<li></li>");
  newLI.addClass("block");
  newLI.attr("taskId", id);

  var newCheckArea = $("<div></div>");
  newCheckArea.addClass("check_area");
  newLI.append(newCheckArea);

  var checkImg = $("<img>");
  checkImg.addClass("check_img");
  checkImg.click(projectTaskClick);

  if(isProjectTaskCompleted(id)) {
    checkImg.attr("src", "cross.png");
  } else {
    checkImg.attr("src", "tick.png");
  }
  newCheckArea.append(checkImg);

  var newTextbox = $("<input type='text'>");
  newTextbox.val(tasks[id].label);
  newTextbox.focusout(textChanged);
  newLI.append(newTextbox);

  $("#task_list").append(newLI);
}

function printAllProjectTasks() {
  for(var i = 0; i < tasks.length; i++) {
    printProjectTask(i);
  }
}

function printUncompletedProjectTasks() {
  for(var i = 0; i < tasks.length; i++) {
    if(!isProjectTaskCompleted(i)) {
      printProjectTask(i);
    }
  }
}

function printNextProjectTasks() {
  for(var i = 0; i < tasks.length; i++) {
    if(isProjectTaskNext(i)) {
      printProjectTask(i);
    }
  }
}

function printCompletedProjectTasks() {
  for(var i = 0; i < tasks.length; i++) {
    if(isProjectTaskCompleted(i)) {
      printProjectTask(i);
    }
  }
}

// ### EVENT HANDLER FUNCTIONS ###


function projectTaskClick(event) {
  var taskId = $(event.target).parent().parent().attr("taskId");
  tasks[taskId].completed = !isProjectTaskCompleted(taskId);
  refresh();
  flush();
}

function newTaskClick(event) {
  var newTaskId = addTask();

  if(taskFilter === 3) {
    tasks[newTaskId].completed = true;
  }

  printProjectTask(newTaskId);
}

function textChanged(event) {
  var taskId = $(event.target).parent().attr("taskId");

  if($(event.target).val() != tasks[taskId]) {
    tasks[taskId].label = $(event.target).val();
    flush();
  }
}

// DEBUG START
//var temId = addTemplate();

//templates[temId].label = "CS 230 Assignment 1 marking"

//var taskId = addTemplateTask(temId);
//templates[0].tasks[0].label = "Has documented the Amount.getAmount() method.";

tasks = projects[0].tasks;

viewLabel = projects[0].label;
viewStage = projects[0].stage;

refresh();


// DEBUG END
