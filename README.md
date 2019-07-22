# Face_Recognition
Built this Face Recognition model for Grace Hopper Celebration, India AI Codeathon 2019.

To run this project on your machine, you should be having Xampp installed on it.
I have used Xampp(Apache) as it makes very easiy for developers to create a web server locally on one's laptop. Also it contains MySQL module, which can be used to create database and later to link that with our code.

## API Used

I coded this using Java Script and PHP. 
For face detection I specifically used Faceapi.js and it's 3 models - SSD Mobilenet V1, Face landmark and Face Recognition model

## Description of the models used

SSD model works as a face detector basically, it detetcts if a human face is present in the image or not. Since my problem statement demanded only one human face per image so I specifically used SingleFaceDetector(). 

Face Landmark model detects the facial landmarks very efficiently, one just have to adjust the size of the canvas chosen equal to the dimensions of the input image.

The last model used is Face Recognition which compares the input face with the faces present in our database and accordinly tells us the results.

## db_images folder

The db_images is the collection of all the images which are stored in our MySQL Datbase (Just like in normal voting system, our voter id's are stored in the databases similarly in our problem, that is the E-voting system, people's aadhar card or Driving License images are stored in the database)

## Test_Voters_WebCam_Images

This folder contains the web scanned images of all the voters who have arived to cast a vote. If their face is matched with a face image present in the database then they will be allowed to vote otherwise not!
