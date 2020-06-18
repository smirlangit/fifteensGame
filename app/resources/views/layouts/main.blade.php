<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link href="/main.css" rel="stylesheet">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<body>
    <div class="container"> 
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
           
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <form class="form-inline my-2 my-lg-0" action="/logout" method="post">   
                    @csrf 
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Выход</button>
                  </form>
            </div>
          
        </nav>
        <div style="height:150px"></div>
        @yield('content')
    </div>
</body>