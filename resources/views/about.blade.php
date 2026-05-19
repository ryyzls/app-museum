<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>

    @vite(['resources/css/about.css'])
</head>
<body>

    <section class="hero">
        <h1>ABOUT US</h1>

        <p>
            Kami adalah website modern yang memberikan pengalaman terbaik
            untuk user dengan tampilan menarik dan fitur yang lengkap.
        </p>
    </section>

    <section class="advantages">

        <h2>KEUNGGULAN WEBSITE</h2>

        <div class="adv-container">

            <div class="adv-card">
                <h3>FAST RESPONSE</h3>
                <p>Website cepat dan ringan digunakan.</p>
            </div>

            <div class="adv-card">
                <h3>UI MENARIK</h3>
                <p>Tampilan modern dan nyaman dilihat.</p>
            </div>

            <div class="adv-card">
                <h3>LATAR BELAKANG</h3>
                <p>Mengikuti website louvre.fr.</p>
            </div>

        </div>

    </section>

    <section class="team-section">

        <h2>OUR TEAM</h2>

        <div class="team-container">

            <div class="team-card">

                <img src="https://i.pravatar.cc/200?img=1">

                <h3>Fahri Arizal</h3>

                <p>Full Stack Developer</p>

                <div class="socials">
                    <a href="#"><img src="{{ asset('images/instagram.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/linkedin.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/gmail.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/github.png') }}"></a>
                </div>

            </div>

            <div class="team-card">

                <img src="https://i.pravatar.cc/200?img=2">

                <h3>Mar'ie RIzqullah</h3>

                <p>Database Administrator</p>

                <div class="socials">
                    <a href="#"><img src="{{ asset('images/instagram.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/linkedin.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/gmail.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/github.png') }}"></a>
                </div>

            </div>

            <div class="team-card">

                <img src="images/juda.png" alt="Juda">

                <h3>Juda Turnip</h3>

                <p>Backend Developer</p>

                <div class="socials">
                    <a href="#"><img src="{{ asset('images/instagram.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/linkedin.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/gmail.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/github.png') }}"></a>
                </div>

            </div>

            <div class="team-card">

                <img src="https://i.pravatar.cc/200?img=4">

                <h3>Azkha Amorie</h3>

                <p>Frontend Developer</p>

                <div class="socials">
                    <a href="#"><img src="{{ asset('images/instagram.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/linkedin.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/gmail.png') }}"></a>

                    <a href="#"><img src="{{ asset('images/github.png') }}"></a>
                </div>

            </div>

        </div>

    </section>

<section class="comment-section">

    <div class="comment-wrapper">

    <h2>KOMENTAR USER</h2>

    <form action="/comment/store" method="POST" class="comment-form">

        @csrf

    <textarea 
    name="message" 
    placeholder="Tambahkan komentar..." 
    required
    rows="1"
    id="commentInput"
></textarea>

        <button type="submit">
            Kirim
        </button>

    </form>

    <div class="youtube-comments">

        @foreach($comments as $comment)

        <div class="youtube-comment">

            <div class="profile-circle">
                {{ strtoupper(substr($comment->name,0,1)) }}
            </div>

            <div class="comment-content">

                <h4>{{ $comment->name }}</h4>

                <p>{{ $comment->message }}</p>

                <div class="comment-actions">

                    <button onclick="toggleEdit({{ $comment->id }})" class="text-btn">
                        Edit
                    </button>

                    <form action="/comment/delete/{{ $comment->id }}" method="POST">

                        @csrf
                        @method('DELETE')

                        <button type="submit" class="text-btn delete-text">
                            Hapus
                        </button>

                    </form>

                </div>

                <div class="edit-form" id="edit-form-{{ $comment->id }}">

                    <form action="/comment/update/{{ $comment->id }}" method="POST">

                        @csrf
                        @method('PUT')

                        <textarea name="message">{{ $comment->message }}</textarea>

                        <button type="submit" class="save-btn">
                            Simpan
                        </button>

                    </form>

                </div>

            </div>

        </div>

        @endforeach

    </div>

    </div>

</section>

<script>

function toggleEdit(id)
{
    const form = document.getElementById('edit-form-' + id);

    if(form.style.display === 'block')
    {
        form.style.display = 'none';
    }
    else
    {
        form.style.display = 'block';
    }
}

</script>

<script>
const textarea = document.getElementById('commentInput');

textarea.addEventListener('input', function(){

    this.style.height = '40px';

    this.style.height = this.scrollHeight + 'px';

});

textarea.addEventListener('keydown', function(e){

    if(e.key === 'Enter' && !e.shiftKey)
    {
        e.preventDefault();

        this.form.submit();
    }

});

</script>

</body>
</html>