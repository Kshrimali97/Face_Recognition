<!-- Source: 
1. https://github.com/justadudewhohacks/face-api.js
 -->

 <?php require_once('connection.php');

//Fetching voters	
$sql      = "SELECT * FROM `voters` ORDER BY vote_status";
$voters   = $conn->query($sql);

$votersArray = array();

if ( $voters->num_rows > 0) {
	while($row = $voters->fetch_assoc()) {
		array_push($votersArray, $row['name'].'__sepvtrdtls__'.$row['filename'].'__sepvtrdtls__'.$row['id']);
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Face Recognition</title>	
	<style type="text/css">
		body{
			text-align: center;
			background: #f2f6f8;
		}
		.img{
			position:absolute;
			z-index:1;
		}

		#container{
		    display:inline-block;
		    width:260px; 
		    height:390px;
		    margin: 0 auto; 
		    background: black; 
		    position:relative; 
		    border:5px solid black; 
		    border-radius: 10px; 
		    box-shadow: 0 5px 50px #333}

		#mycanvas{
		    position:relative;
		    z-index:20;
		}
		#waiting_load{
		  display: none;
		  color: #FFFFFF;
		    width:100%; 
		    height:2000px; 
		    z-index:11; 
		    position:absolute;
		    top:0px;
		    left:0px;
		    text-align:center;
		    font-family:"Times New Roman",Georgia,Serif;
		}
	</style>
</head>
<body>
	<div id='waiting_load' style="background:url('/face_recog/images/opacity.png')">  
        <img src="/face_recog/images/loading_big.gif" style="margin-top: 10px;">
    </div> 
	<center>
		<h2 style="color: blue">Welcome to E-Voting System</h2>
		<br>
		<fieldset style="text-align: center;width: 70%;margin: auto;background-color: lightgray;border-color: red;">
			<legend>Face Recognition: Image Upload</legend>
	    		Upload Image: <input id="myFileUpload" type="file" onchange="uploadImage()" accept=".jpg, .jpeg, .png">
	    		<br><br>
	    		<b style="font-size: 20px;">Name: <span id='name_voter'></span></b>
	    		<br><br>
	    		<div id="container">	    			
		    		<img id="myImg" class='img' src="" alt="New Image" width="260" height="390"/>
		      		<canvas id="mycanvas" width="260" height="390"></canvas>
		      	</div>
		      	<br>		      	
	  	</fieldset>
	</center>

	<script src="face-api.min.js"></script>				
	<script src="jquery.min.js"></script>
	<script type="text/javascript">
		async function uploadImage() {
		  	const imgFile = document.getElementById('myFileUpload').files[0]		
		  	const img = await faceapi.bufferToImage(imgFile)
		  	document.getElementById('myImg').src = img.src
		  	document.getElementById('waiting_load').style.display='block';
		  	document.getElementById('name_voter').innerHTML  = '';
		  	var mycanvas = document.getElementById('mycanvas');
		  	var context = mycanvas.getContext('2d');     

      		context.clearRect(0, 0, mycanvas.width, mycanvas.height);
    
		  	start()
		}
		  
		async function start() {

			//Face-api.js is a JavaScript API for face detection and face recognition in the browser implemented on top of the tensorflow.js core API
			// It makes use of the tensorflow.js core API for its proper working inside the web browser. You can use this library to track and detect a face in real-time.
			//It implements a series of convolutional neural networks (CNNs), optimized for the web and for mobile devices.
			//Load models
			const MODEL_URL = '/face_recog/models'
			await faceapi.loadSsdMobilenetv1Model(MODEL_URL)   //loading the different models like ssdmobile, face landmarks and face recognition model
			await faceapi.loadFaceLandmarkModel(MODEL_URL)
			await faceapi.loadFaceRecognitionModel(MODEL_URL)

			//For face detection, face-api.js implements the models SSD Mobilenet V1, SSD (Single Shot Multibox Detector) MobileNet V1 is a model based on MobileNet V1 that aims to obtain high accuracy in detecting face bounding boxes. This model basically computes the locations of each face in an image and returns the bounding boxes together with its probability for each face detected.

			//API allows software to communicate with another software.

			//Input by selecting a test image assuming that it is taken by a web cam 
			const input = document.getElementById('myImg')					
			const displaySize = { width: input.width, height: input.height }

			// Resize the overlay canvas to the input dimensions
			const canvas = document.getElementById('mycanvas')
			faceapi.matchDimensions(canvas, displaySize)			

			//Face Detection using the face api along with
			const fullFaceDescription = await faceapi.detectSingleFace(input).withFaceLandmarks().withFaceDescriptor()
			if (!fullFaceDescription) {
		      alert(`No face detected for new image, please try again!`)
		      document.getElementById('waiting_load').style.display='none'
		      return
		    }
			else{
				var flag = false;
				var flag2 = false;
				const resizedDetection = faceapi.resizeResults(fullFaceDescription, displaySize)		//detects the face present in our input image
				faceapi.draw.drawDetections(canvas, resizedDetection)
				faceapi.draw.drawFaceLandmarks(canvas, resizedDetection)

				//Match image in database				
				var labels = <?php echo json_encode($votersArray); ?>; // Converting PHP array to javascript array
				var lableArr = [];
				await Promise.all(
				  	labels.map(async label => {
					    // fetch image data from urls and convert blob to HTMLImage element
					    lableArr      = label.split("__sepvtrdtls__");
					    const imgUrl  = lableArr[1];						//storing the image path at index 1
					    const imgName = lableArr[0];						//storing the image name at index 0	
					    const imgid   = lableArr[2];						// storing the image id at index 2
					    const img     = await faceapi.fetchImage(imgUrl)
					    
					    // detect the face with the highest score in the image and compute it's landmarks and face descriptor
					    //Using SingleFace Detection
					    const fullFaceDescription2 = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor()
					    
					    if (!fullFaceDescription2) {
					      console.log(`Msg: No faces detected for ${label}`)
					    }
					    else{
					    	const maxDescriptorDistance = 0.6
					    	const faceMatcher = new faceapi.FaceMatcher(fullFaceDescription2, maxDescriptorDistance)
							const bestMatch = faceMatcher.findBestMatch(fullFaceDescription.descriptor)
							
							if( bestMatch.toString().indexOf("unknown") != -1){							    
							    //No match with current image
							}	
							else{
								await jQuery.ajax({
									type: "POST",
									url: "votercheck.php",
									data: 'id='+imgid+'&tokenupdtvtr=yes',
									success: function(data) {
										if( data == 'notvoted' ){
											flag2 = true;
											console.log("Matched and not voted")
										}
										else if( data == 'voted' ){
											console.log("Matched and voted!")
										}
									}
								});
								
								document.getElementById('name_voter').innerHTML  = imgName
								flag = true;
							    return false;
							}		    	
					    }
				  	})
				)

				if( !flag ){
					document.getElementById('waiting_load').style.display='none';
					alert("No match found!");					
					return
				}
				else{ 
					if( !flag2 ){
						alert("Face matched but not allowed for voting as the voter has already voted!");	
					}
					else{
						alert("Face matched and allowed for voting!");
					}
					document.getElementById('waiting_load').style.display='none';
					
					return
				}
			}	
		}		
	</script>
</body>
</html>