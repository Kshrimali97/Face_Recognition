## Face_Recognition
Built this Face Recognition model for Grace Hopper Celebration, India AI Codeathon 2019.

To run this project on your machine, you should be having Xampp installed on it.
I have used Xampp as it makes very easiy for developers to create a web server locally on one's laptop. Also it contains MySQL module, which can be used to create database and later to link that with our code.

# API Used

I coded this using Java Script and PHP. 
For face detection I specifically used Faceapi.js and it's 3 models - SSD Mobilenet V1, Face landmark and Face Recognition model

# Description of the models used

SDD model works as a face detector basically, it detetcts if a human face is present in the image or not. Since my problem statement demanded only one human face per image so I specifically used SingleFaceDetector(). 

Face Landmark model detects the facial landmarks very efficiently, one just have to adjust the size of the canvas chosen equal to the dimension of the input image.

The last model used is Face Recognition which compares the input face with the faces present in our database and accordinly tells us the results.
