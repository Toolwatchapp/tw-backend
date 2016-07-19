$(function() {

  var http = location.protocol;
  var slashes = http.concat("//");
  var host = slashes.concat(window.location.hostname);

  var optionsBrand = {
  	url: host + "/assets/json/watch-brand.json",
    getValue: "name",
  	list: {
  		match: {
  			enabled: true
  		},
      onSelectItemEvent: function() {

  			var model = $("#brand").getSelectedItemData().models;
        var optionModel = {
          url: host + "/assets/json/watch-models/"+model+".json",
          list: {
        		match: {
        			enabled: true
        		}
          }
        };

        $("#model").easyAutocomplete(optionModel);

  		}
  	},
    template: {
  		type: "iconRight",
  		fields: {
  			iconSrc: "icon"
  		}
  	},

  };

  $("#brand").easyAutocomplete(optionsBrand);


    //
    // var availableBrands = [
    //   "ActionScript",
    //   "AppleScript",
    //   "Asp",
    //   "BASIC",
    //   "C",
    //   "C++",
    //   "Clojure",
    //   "COBOL",
    //   "ColdFusion",
    //   "Erlang",
    //   "Fortran",
    //   "Groovy",
    //   "Haskell",
    //   "Java",
    //   "JavaScript",
    //   "Lisp",
    //   "Perl",
    //   "PHP",
    //   "Python",
    //   "Ruby",
    //   "Scala",
    //   "Scheme"
    // ];
    //
    // var availableModelsByBrand = {};
    // availableModelsByBrand["Java"] = ["Niark"];
    // availableModelsByBrand["Scala"] = ["Plop"];
    //
    // $( "#brand" ).autocomplete({
    //   source: availableBrands,
    //   autoFill: true,
    //   select: function (event, ui){
    //     console.log(ui.item.value);
    //     var brand = ui.item.value;
    //
    //     $("#model").autocomplete({
    //       autoFill: true,
    //       source: availableModelsByBrand[brand]
    //     });
    //   }
    // });
  });
