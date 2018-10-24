const validPasscodes = ['amiGroot'];
let authenticatted = false;

function addMatchesFromInput() {
  const gameWeek = $("#FfpbMatchGameweek").val();
  const entry1SubgroupPostion = $("#FfpbMatchEntry1SubgroupPostion").val();
  const entry2SubgroupPostion = $("#FfpbMatchEntry2SubgroupPostion").val();
  $.ajax({
    url: `${myBaseUrl}/FfpbMatches/addMatchesByGameweek`,
    type: 'POST',
    dataType: 'JSON',
    data: {entry1SubgroupPostion, entry2SubgroupPostion, gameWeek},
    success: function (data) {
      console.log(data);
  },
    error: function(err) {console.log(err)},
  });
}
console.log(myBaseUrl);
$("form").submit(function(e) {
  e.preventDefault();
  if(!authenticatted && false) {
    $.confirm({
      title: 'Please Identify!',
      content: '' +
      '<form action="" class="formName">' +
      '<div class="form-group">' +
      '<label>Give your passcode</label>' +
      '<input type="text" placeholder="Your passcode" class="passcode form-control" required />' +
      '</div>' +
      '</form>',
      buttons: {
          formSubmit: {
              text: 'Submit',
              btnClass: 'btn-blue',
              action: function () {
                  const givenPasscode = this.$content.find('.passcode').val();
                  console.log(validPasscodes.includes(givenPasscode));
                  if(!validPasscodes.includes(givenPasscode)){
                      $.alert('provide a valid passcode');
                      return false;
                  }

                  authenticatted =true;
                  addMatchesFromInput();
              }
          },
          cancel: function () {
              //close
          },
      },
      onContentReady: function () {
          // bind to events
          var jc = this;
          this.$content.find('form').on('submit', function (e) {
              // if the user submits the form by pressing enter in the field.
              e.preventDefault();
              jc.$$formSubmit.trigger('click'); // reference the button and click it
          });
      }
    });
  } else {
    addMatchesFromInput();
  }
    
});

  