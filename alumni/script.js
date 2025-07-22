const hidden_details_btn = document.getElementById('hidden_details');
const displayed_details = document.getElementById('displayed_details');

hidden_details_btn.addEventListener("click", (e) => {
    const fetched_details = document.getElementById('socialdetails');
    const show_posts = document.getElementById('post');
    fetched_details.style.display = 'none';
    show_posts.style.display = 'block';
})

displayed_details.addEventListener("click", (e) => {
    const fetched_details = document.getElementById('socialdetails');
    const show_posts = document.getElementById('post');
    fetched_details.style.display = 'block';
    show_posts.style.display = 'none';
})
