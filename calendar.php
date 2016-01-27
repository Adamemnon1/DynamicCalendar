<!DOCTYPE html>
<html>
    <head>
        <title>Calendar</title>
        <style>
            body{
                text-align: center;
            }
            table {
                width: 600px;
                height:400px;
            }

            th {
                background-color: blue;
                color: white;         
            }

        </style>
    </head>
    <body>
    	<form id="form">
			<div class="userInfo">
				Username: <input type="text" id="username"/><br/>
				Password: <input type="text" id="password1"/><br/>
			</div>
			<input type="button" value="Log in" id="login"/><br/>
			<input type="button" value="Sign Up" id="register"/>
		</form>
        <form>
            <input type="hidden" value="Search" id="search"/>
            <input type="hidden" value="" id="searchText"/>
            <div id="searchDisplay"><div id="display1"></div></div>
            <br/>
            <br/>
            <input type="hidden" value="Log out" id="logout"/>
            <input type="button" value="<" id="previous"/>
            <input type="button" value=">" id="next"/>
            <input type="hidden" id="token" value=""/>
        </form>
        <div id="calendar"></div>
        <br/>

        <form id="events">
            <div id="event"></div>
            <input type="hidden" value="Delete" id="delete"/>
            <input type="hidden" value="Edit" id="edit"/>
            <input type="hidden" value="" id="eventID"/>
            <br/>
            <input type="hidden" value="" id="editTitle"/>
            <br/>
            <input type="hidden" value="" id="editTime"/>
            <br/>
            <input type="hidden" value="" id="editViewer"/>
            <input type="hidden" value="Update" id="update"/>
        </form>

        <form id = "input">
            <div id="dateSelect"></div>
            <br/>
            Add your events here:
            <br/>
            Title: <input type="text" id="title"/><br/>
            Time: <input type="time" id="time"/><br/>
            Level of Importance: 
            <select id="importance">
              <option value="red">Very Important (red)</option>
              <option value="yellow">Important (yellow)</option>
              <option value="green">Not Important (green)</option>
            </select>
            <br/>
            Viewer (separate by comma): 
            <input type="text" id="viewer"/>
            <input type="button" value="Add" id="add"/>
            <input type="hidden" id ="dateEvent" value=""/>
        </form>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js" type="text/javascript"></script>
        <script src="http://classes.engineering.wustl.edu/cse330/content/calendar.min.js" type="text/javascript"></script>

        <?php
            ini_set("session.cookie_httponly", 1);
            session_start();
            if (isset($_SESSION["username"])){//checks if the user is logged in or not
        ?>
            <script>
                $("#form").hide();
                $("body").prepend("<h1 id='h1'>You are logged in as </h1>");
                var name = "<?php echo $_SESSION["username"]; ?>";
                $("h1").append(name);
                document.getElementById("logout").setAttribute("type","button");
                document.getElementById("search").setAttribute("type","button");
                document.getElementById("searchText").setAttribute("type","text");
                $("#input").hide();
                var loginstatus=1;
                document.getElementById("token").value="<?php echo $_SESSION['token']; ?>";
            </script>
        <?php
            }else{
        ?>
            <script>
                $("#input").hide();
            </script>
        <?php
            }
        ?>

                        


        <script>
            var numOfDays=[31,28,31,30,31,30,31,31,30,31,30,31];           
            var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
            var today = new Date();
            var dayOfWeek = today.getDay();
            var date = today.getDate();
            var month = today.getMonth();
            var year = 1900+today.getYear();//Google Chrome's year is the current year-1900, need ot account for that
            var indicator = today;
            var day=1;

            function display(){   
                var ids = [];   
                indicator.setDate(1);
                var dayOfWeek1 = indicator.getDay();//the above two lines determine what day the first day of the month is
                var htmlString="";
                if (((year % 4 == 0) && (year % 100 !=0)) || (year % 400 == 0) ){
                    numOfDays[1]=29;//accounts for leap years
                }else{
                    numOfDays[1]=28;;
                }
                htmlString=htmlString+"<br><table border=2 align='center' id = 'calendar_table'> <tr><th align=center colspan=7>" + months[month] + " " + year + "</th></tr>";
                htmlString=htmlString+"<tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>";
                for (var i = 0; i < 6; i++) {//the loops below finishes the rest of the table
                    htmlString=htmlString+"<tr>";
                    for (var j = 0; j < 7; j++) {
                        if ((i == 0 && j < dayOfWeek1) || (day > numOfDays[month])) {
                            htmlString=htmlString+"<td><br></td>";//empty spaces are added to the beginning and the end of the table
                        } else {
                            var monthRight=month+1;//the month array's index is one smaller than the real month number
                            var dayId=year+"/"+monthRight+"/"+day;
                            ids.push(dayId);
                            htmlString=htmlString+"<td id="+dayId+">"+ day + "</td>";
                            day++;
                        }
                    }
                    htmlString=htmlString+"</tr>";
                }
                htmlString=htmlString+"</table>";
                document.getElementById("calendar").innerHTML +="<div id='content'></div>";//insert the completed table into the html file
                document.getElementById("content").innerHTML += htmlString;
                for (var k = 0; k<numOfDays[month]; k++){
                    var _id=""+ids[k];
                    if (typeof loginstatus != "undefined"){
                        document.getElementById(_id).addEventListener("click",details,false);
                        highlight();
                    }
                    
                }
               
            } 

            function highlight(){//this function sets the background colors of event cells that have different level of importance
                var monthCurrent="month="+encodeURIComponent(month);
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST","highlight.php",true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function(event){
                    var jsonData = JSON.parse(event.target.responseText);
                    
                    for (var i = 0; i<jsonData.length; i++){
                        if (jsonData[i].importance=="red"){
                            document.getElementById(jsonData[i].date).setAttribute("bgcolor", "red");
                        }else if(jsonData[i].importance=="yellow"){
                             document.getElementById(jsonData[i].date).setAttribute("bgcolor", "yellow");
                        }else{
                            document.getElementById(jsonData[i].date).setAttribute("bgcolor", "green");
                        }
                    }
                },false);
                xmlHttp.send(monthCurrent); 
            }


            function details(event){
                $("#input").show();
                $("#dateSelect").remove();
                $("#input").prepend("<div id='dateSelect'></div>");
                $("#dateSelect").prepend(document.createTextNode("The date you chose was "+ event.target.id));
                document.getElementById("dateEvent").setAttribute("value", event.target.id);


                var dateChosen="dateChosen="+encodeURIComponent(event.target.id);
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST","retrieve.php",true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function(event){
                    var jsonData = JSON.parse(event.target.responseText); 
                    for (var i = 0; i<jsonData.length; i++){
                        $("#event").prepend("title: "+jsonData[i].title+"  time: "+jsonData[i].time+" id:  "+jsonData[i].id +"<br/>");
                    }
                },false);
                xmlHttp.send(dateChosen); 
                document.getElementById("add").addEventListener("click",addEvent,false);


                $("#event").remove();
                $("#events").prepend("<div id='event'></div>");
                document.getElementById("delete").setAttribute("type", "button");
                document.getElementById("edit").setAttribute("type", "button");
                document.getElementById("eventID").setAttribute("type", "text");
                document.getElementById("delete").addEventListener("click",deleteEvent ,false);
                document.getElementById("edit").addEventListener("click",editEvent ,false);
                
            }

            function deleteEvent(event){
                var id = document.getElementById("eventID").value;
                var token = document.getElementById("token").value;
                var eventID = "id="+encodeURIComponent(id)+"&token="+encodeURIComponent(token);
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST","delete.php",true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function(event){
                    var jsonData = JSON.parse(event.target.responseText); 
                    alert(jsonData.message);                            
                },false);
                xmlHttp.send(eventID); 
                location.reload();
            }




            function editEvent(){//this function displays the details of the chosen events and makes them editable
                var idChosen="idChosen="+encodeURIComponent(document.getElementById("eventID").value);
                var titleToEdit="";
                var timeToEdit="";
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST","populate.php",true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function(event){
                    var jsonData = JSON.parse(event.target.responseText); 
                    if (jsonData.message!="failure"){
                        titleToEdit= String(jsonData.title);
                        timeToEdit=String(jsonData.time);
                        viewerToEdit=String(jsonData.viewer);
                        document.getElementById("editTitle").value=titleToEdit;
                        document.getElementById("editTime").value=timeToEdit;
                        document.getElementById("editViewer").value=viewerToEdit;
                        document.getElementById("update").setAttribute("type","button");
                    }
 
                },false);
                xmlHttp.send(idChosen); 
                document.getElementById("update").addEventListener("click",updateEvent,false); 
                document.getElementById("editTitle").setAttribute("type","text");
                document.getElementById("editTime").setAttribute("type","time");
                document.getElementById("editViewer").setAttribute("type","text");
            }


            function updateEvent(){
                var title = document.getElementById("editTitle").value;
                var time = document.getElementById("editTime").value;
                var viewer=document.getElementById("editViewer").value;
                var token = document.getElementById("token").value;
                var eventdetail = "title="+encodeURIComponent(title)+"&time="+encodeURIComponent(time)+"&idChosen="+encodeURIComponent(document.getElementById("eventID").value)+"&viewer="+encodeURIComponent(viewer)+"&token="+encodeURIComponent(token);
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST","edit.php",true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function(event){
                    var jsonData = JSON.parse(event.target.responseText); 
                    alert(jsonData.message);
                },false);
                xmlHttp.send(eventdetail); 
            }




            function addEvent(){
                var title = document.getElementById("title").value;
                var time = document.getElementById("time").value;
                var dateEvent=document.getElementById("dateEvent").value;
                var importance=document.getElementById("importance").value;
                var viewer=document.getElementById("viewer").value;
                var eventdetail = "title="+encodeURIComponent(title)+"&time="+encodeURIComponent(time)+"&dateEvent="+encodeURIComponent(dateEvent)+"&importance="+encodeURIComponent(importance)+"&viewer="+encodeURIComponent(viewer);
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST","add.php",true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function(event){
                    var jsonData = JSON.parse(event.target.responseText); 
                    alert(jsonData.message);     
                    location.reload();                            
                },false);
                xmlHttp.send(eventdetail); 
                highlight();
            }


        	function signUp(event){
        		var username = document.getElementById("username").value;
        		var password1 = document.getElementById("password1").value;
        		var password2 =document.getElementById("password2").value;
        		var signupdetail = "username="+encodeURIComponent(username)+"&password1="+encodeURIComponent(password1)+"&password2="+encodeURIComponent(password2);
        		var xmlHttp = new XMLHttpRequest();
        		xmlHttp.open("POST","registration.php",true);
        		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        		xmlHttp.addEventListener("load", function(event){
        			var jsonData = JSON.parse(event.target.responseText);
        			alert(jsonData.message);
        			if (jsonData.success){
        				$("#form").hide();
        				var name=jsonData.username;
        				$("body").prepend("<h1>You are logged in as </h1>");
        				$("h1").append(name);
                        document.getElementById("logout").setAttribute("type","button");
                        location.reload();
        			}
        		},false);
        		xmlHttp.send(signupdetail);	
        	}


        	function pwdVerification(event){
        		var pwdBox=document.createElement("input");
        		pwdBox.setAttribute("type","text");
        		pwdBox.setAttribute("id","password2");
        		var submit = document.createElement("input");
        		submit.setAttribute("type","button");
        		submit.setAttribute("id","submit");
        		submit.setAttribute("value","Submit");
        		document.getElementsByClassName("userInfo")[0].appendChild(document.createTextNode("Enter yoru password again"));
        		document.getElementsByClassName("userInfo")[0].appendChild(pwdBox);
        		document.getElementsByClassName("userInfo")[0].appendChild(submit);
        		document.getElementById("submit").addEventListener("click",signUp,false);
        		document.getElementById("form").removeChild(document.getElementById("register"));
                document.getElementById("form").removeChild(document.getElementById("login"));
        	}	


            function search(){
                $("#display1").remove();
                $("#searchDisplay").append("<div id='display1'></div>"); 
                var titleInput="title="+encodeURIComponent(document.getElementById("searchText").value);
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST","search.php",true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function(event){
                    var jsonData = JSON.parse(event.target.responseText); 
                    for (var i = 0; i<jsonData.length; i++){
                        $("#display1").append("title: "+jsonData[i].title+"  date:  "+jsonData[i].date+ "  time: "+jsonData[i].time+" id:  "+jsonData[i].id +"<br/>");
                    }
                },false);
                xmlHttp.send(titleInput);       
            }

        	function login(){
        		var username = document.getElementById("username").value;
        		var password = document.getElementById("password1").value;

        		var userCred = "username="+encodeURIComponent(username)+"&password="+encodeURIComponent(password);
        		var xmlHttp = new XMLHttpRequest();
        		xmlHttp.open("POST","login.php",true);
        		xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        		xmlHttp.addEventListener("load", function(event){
        			var jsonData = JSON.parse(event.target.responseText);
        			alert(jsonData.message);      			        			
        			if (jsonData.success){
        				$("#form").hide();
        				var name=jsonData.username;
        				$("body").prepend("<h1>You are logged in as </h1>");
        				$("h1").append(name);
                        document.getElementById("logout").setAttribute("type","button");
                        document.getElementById("search").setAttribute("type","button");
                        document.getElementById("searchText").setAttribute("type","text");
                        location.reload();
                $("#input").hide();
        			}

        		},false);
        		xmlHttp.send(userCred);	
        	}

            function logout(){
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.open("POST","logout.php",true);
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xmlHttp.addEventListener("load", function(){
                    location.reload();
                },false);
                xmlHttp.send(null); 
                $("#form").show();
                document.getElementById("logout").setAttribute("type","hidden");
                $("#h1").remove();
                $("#display1").remove();
                document.getElementById("editTitle").setAttribute("type","hidden");
                document.getElementById("editTime").setAttribute("type","hidden");
                document.getElementById("editViewer").setAttribute("type","hidden");
                document.getElementById("update").setAttribute("type","hidden");
                document.getElementById("delete").setAttribute("type", "hidden");
                document.getElementById("edit").setAttribute("type", "hidden");
                document.getElementById("eventID").setAttribute("type", "hidden");
                document.getElementById("search").setAttribute("type","hidden");
                document.getElementById("searchText").setAttribute("type","hidden");
                $("#event").remove();
                $("#dateSelect").remove();
                $("#input").hide();
            }

            function back(){
                day=1;
                month-=1;//updates the month for display
                if (month<0){//accounts for the changing of the year
                    month+=12;
                    year-=1;
                }    
                indicator.setMonth(month);
                indicator.setYear(year);
                $('#content').remove();
                display();
            }
            function forward(){
                day=1;
                month+=1;
                console.log(month);
                if (month>11){
                    month-=12;
                    year+=1;
                }
                indicator.setMonth(month);
                indicator.setYear(year);
                $('#content').remove();
                display();
            }
            display();
        	document.getElementById("register").addEventListener("click",pwdVerification,false);
        	document.getElementById("login").addEventListener("click",login,false);
            document.getElementById("logout").addEventListener("click",logout,false);
            document.getElementById("search").addEventListener("click",search,false);
            document.getElementById("previous").addEventListener("click",back,false);
            document.getElementById("next").addEventListener("click",forward,false);
        </script>
    </body>
</html>
