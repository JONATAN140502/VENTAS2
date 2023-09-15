<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENIEC PERÚ</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>

<body>
    <center>
        <br>
        <div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"> DNI CONSULTA </h3>
    </div>
        <div class="btn-group">
            <label for="">Tipo(*): </label>
     <select  name="tipe" id="tipe" class="form-control selectpicker" required>
       <option value="DNI">DNI</option>
       <option value="RUC">RUC</option>
     </select></div>
 <div class="btn-group">
            <input type="text" id="documento" class="form-control">
            <button type="button" class="btn btn-success" id="buscar">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                </svg>
            </button>
        </div>
        <br><br>
        <div class="row">

           <div class="col-sm-10"></div>
            
             <div class="col-sm-5"> DNI</div>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="DOC" disabled>
            </div>
              <div class="col-sm-10"></div>
              <div class="col-sm-5"> Nombre Completo </div>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="nom" disabled>
            </div>
              <div class="col-sm-10"></div>
        </div>
        </div>
    </center>


</body>

<script>
    $('#buscar').click(function(){
        dni=$('#documento').val();
        tipo=$('#tipe').val();
        console.log('BUSCANDO')
        $.ajax({
           url:'Controlador/consultaDNI.php',
           type:'post',
             data: { tipo: tipo, dni: dni },
           dataType:'json',
           success:function(r){
                console.log('ENTRANDOCANDO')
            if(r.numeroDocumento==dni){
                
                  $('#DOC').val(r.numeroDocumento);
                  $('#nom').val(r.nombre);
                  console.log("vlaido");
            }else  if(r=''){
                
            }
            else{
                alert(r.error);
                 console.log(r.error)
                  console.log(r)
            }
            console.log(r)
            $("#documento").val("");
           }
        });
    });
</script>


</html>