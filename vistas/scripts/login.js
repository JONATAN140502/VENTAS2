$("#frmAcceso").on('submit', function(e)
{
	e.preventDefault();
	logina=$("#logina").val();
	clavea=$("#clavea").val();

	$.post("../ajax/usuario.php?op=verificar",
        {"logina":logina, "clavea":clavea},
        function(data)
        {
           if (data!="null")
            {
                console.log(data);
            	$(location).attr("href","escritorio.php");
                bootbox.alert("Bienvenido");
            }else{
            	bootbox.alert("Usuario y/o Password incorrectos");
            }
        });
})