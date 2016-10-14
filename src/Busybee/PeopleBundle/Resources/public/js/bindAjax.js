$(document).ready(function(){
 
  var forms = [
    '[ name="{{ person_form.vars.full_name }}"]'
  ];
 
  $( forms.join(',') ).submit( function( e ){
    e.preventDefault();
 
    postForm( $(this), function( response ){
    });
 
    return false;
  });
 
});
