<!doctype html>
	<head>
		<title>Show Notification using Javascript</title>
	</head>
	<body>
		<h3>Show Notification using Javascript</h3>


		<script>
			/* JS comes here */
			askForApproval();
			
            function askForApproval() {
                if(Notification.permission === "granted") {
                    createNotification('Wow! This is great', 'created by @study.tonight', 'https://www.studytonight.com/css/resource.v2/icons/studytonight/st-icon-dark.png');
                }
                else {
                    Notification.requestPermission(permission => {
                        if(permission === 'granted') {
                            createNotification('Wow! This is great', 'created by @study.tonight', 'https://www.studytonight.com/css/resource.v2/icons/studytonight/st-icon-dark.png');
                        }
                    });
                }
            }
            
            function createNotification(title, text, icon) {
                const noti = new Notification(title, {
                    body: text,
                    icon
                });
            }
		</script>
	</body>
</html>