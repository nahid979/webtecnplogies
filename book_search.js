function searchBooks() {
    let keyword = document.getElementById('search_input').value.trim();
    if (keyword === "") {
        location.reload();
        return;
    }

    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log("Response from server:", xhr.responseText); // কনসোলে দেখান
            try {
                let books = JSON.parse(xhr.responseText);
                updateBookTable(books);
            } catch(e) {
                console.error("JSON Parse Error:", e);
                alert("Error parsing response. Check console.");
            }
        }
    };
    xhr.open("GET", "/-Web_Project_Spring_25_26_G2/Controller/api_search_books.php?q=" + encodeURIComponent(keyword), true);
    xhr.send();
}

function updateBookTable(books) {
    let tbody = document.getElementById('books_table_body');
    tbody.innerHTML = '';
    if (books.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6">No books found</td></tr>';
        return;
    }
    for (let b of books) {
        let rowClass = (b.available_copies == 0) ? 'style="background:#ffcccc"' : '';
        let row = `<tr ${rowClass}>
            <td>${escapeHtml(b.title)}</td>
            <td>${escapeHtml(b.author)}</td>
            <td>${escapeHtml(b.genre_name)}</td>
            <td>${b.total_copies}</td>
            <td>${b.available_copies}</td>
            <td>
                <a href="books.php?edit=${b.id}">Edit</a> |
                <a href="../Controller/BookController.php?delete=${b.id}" onclick="return confirm('Delete?')">Delete</a>
            </td>
        </tr>`;
        tbody.innerHTML += row;
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}