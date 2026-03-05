function addReview(){

let name = document.getElementById("name").value;
let rating = document.getElementById("rating").value;
let comment = document.getElementById("comment").value;

if(name === "" || rating === "" || comment === ""){
alert("Please fill all fields");
return;
}

let stars = "★".repeat(rating);

let reviewHTML = `
<div class="review-card">
<h3>${name}</h3>
<div class="stars">${stars}</div>
<p class="comment">${comment}</p>
</div>
`;

document.getElementById("reviewsContainer").innerHTML += reviewHTML;

document.getElementById("name").value = "";
document.getElementById("rating").value = "";
document.getElementById("comment").value = "";

}