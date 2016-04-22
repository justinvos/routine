// DEBUG SETUP
account = 0;
key = "ff5eaf22921ae0fb83917aefb2bb0ac5";

updateTemplates();

function updateTemplates() {
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
      for(i = 0; i < result.templates.length; i++) {
        var template = document.createElement("li");
        template.innerHTML = result["templates"][i]["label"];
        template.setAttribute("template", result["templates"][i]["id"]);
        templateList.appendChild(template);
      }
    }
  });
}
