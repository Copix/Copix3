<form id="wsdlform" action="{copixurl dest='webserviceclient||test'}" method="post">
    <input type="text" name="name" />
    <label for="wsdl">Wsdl : </label><input value="http://www.alptis.org/portail/index.php/ewrapper/webservice/wsdlexample?wsdl" type="text" name="wsdl" id="wsdl" />
    <br />
    <input type="checkbox" name="options">
    <br />
    <input type="text" name="login" />
    <br />
    <input type="text" name="password" />
    <br />
    <input type="button" id="update" value="Tester"/>
</form>
<div id="return">

</div>
{copixhtmlheader kind="jsdomreadycode" }
{literal}

$('update').addEvent ('click', function () {
    new Request.HTML ({
        url:$('wsdlform').get('action'),
        data:$('wsdlform'),
        method:'post',
        update:$('return')
    }).send ();
});


{/literal}
{/copixhtmlheader}