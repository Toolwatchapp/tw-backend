$(function() {
    var availableBrands = [
      "ActionScript",
      "AppleScript",
      "Asp",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme"
    ];

    var availableModelsByBrand = {};
    availableModelsByBrand["Java"] = ["Niark"];
    availableModelsByBrand["Scala"] = ["Plop"];

    $( "#brand" ).autocomplete({
      source: availableBrands,
      autoFill: true,
      select: function (event, ui){
        console.log(ui.item.value);
        var brand = ui.item.value;

        $("#model").autocomplete({
          autoFill: true,
          source: availableModelsByBrand[brand]
        });
      }
    });
  });
