<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="/css/main.css">
        <link rel="stylesheet" href="/css/framework.css">
	<link href="/images/favicon.png" rel="icon" type="image/png">
        <title>Система управления версиями - devadmin</title>
    </head>
    <body>
        <div class="g">
            <div class ="f-nav-bar f-nav-bar-fixed">
                            <div class="f-nav-bar-body">
                                <div class="f-nav-bar-title">DevAdm</div>
					<ul class="f-nav" id="nav">
						<li>{PROJECT_ADD}</li>
						<li>{PROJECT_EDIT}</li>
						<li>{USERS_EDIT}</li>
					</ul><!--f-nav-->
                               		<ul class="f-nav f-nav-right">
                                    	<li>Вы вошли как: {USERNAME}</li>
                                    	<li><a href="logout.php">выход</a></li>
                               		</ul>   
                            </div> <!--f-nav-bar-body-->
            </div>                        
            <div class="g-row">
             
                      <section id="fortable">
                                    <table>
                                        <thead> 
                                            <tr>
                                                <th>Название проекта</th>

                                                <!--th>Работа с ревизиями</th!-->

                                                <th>SVN Up боевого сайта</th>


                                                <th>SVN UP девелоперского сайта</th>


                                                <th>Копирование боевой базы данных в девелоперскую</th>
                                            </tr>
                                        </thead>
                                                <tbody>

                                                {CONTENT}


                                                </tbody>    
                                    </table>


                     </section> <!--For table !-->
						<section>
						{POST_MESSAGES}
						</section>
                            </div>
                   </div>
          
        {ADDITIONAL}
 </body>
</html>
