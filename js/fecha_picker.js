 $(function () {
        window.prettyPrint && prettyPrint();
        $('#fecha').fdatepicker({
          format: 'dd-mm-yyyy'
        });

        $('#dp2').fdatepicker({
          closeButton: true
        });

        $('#dp3').fdatepicker();
        $('#dp3').fdatepicker();
        $('#dp-margin').fdatepicker();
        $('#dpYears').fdatepicker();
        $('#dpMonths').fdatepicker();
        var startDate = new Date(2012, 1, 20);
        var endDate = new Date(2012, 1, 25);
        $('#dp4').fdatepicker()
          .on('changeDate', function (ev) {
          if (ev.date.valueOf() > endDate.valueOf()) {
            $('#alert').show().find('strong').text('The start date can not be greater then the end date');
          } else {
            $('#alert').hide();
            startDate = new Date(ev.date);
            $('#startDate').text($('#dp4').data('date'));
          }
          $('#dp4').fdatepicker('hide');
        });

        $('#dp5').fdatepicker()
          .on('changeDate', function (ev) {
          if (ev.date.valueOf() < startDate.valueOf()) {
            $('#alert').show().find('strong').text('The end date can not be less then the start date');
          } else {
            $('#alert').hide();
            endDate = new Date(ev.date);
            $('#endDate').text($('#dp5').data('date'));
          }
          $('#dp5').fdatepicker('hide');
        });
       

        // implementation of disabled form fields
      
  
        var nowTemp = new Date();
        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
        var checkin = $('#fecha').fdatepicker({
          onRender: function (date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
          }
        }).on('changeDate', function (ev) {
          if (ev.date.valueOf() > checkout.date.valueOf()) {
            var newDate = new Date(ev.date)
            newDate.setDate(newDate.getDate() + 1);
            checkout.setValue(newDate);
          }



          checkin.hide();
          $('#dpd2')[0].focus();
        }).data('datepicker');

        var checkout = $('#dpd2').fdatepicker({
          onRender: function (date) {
            return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
          }
        }).on('changeDate', function (ev) {
          checkout.hide();
        }).data('datepicker');
      });
    