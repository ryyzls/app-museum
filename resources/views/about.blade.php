<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('components.navbar')

    <section class="hero">
        <h1>ABOUT US</h1>

        <p style="max-width: 85%; width: 100%; margin: 0 auto; line-height: 1.6;">
            Just as the Musée du Louvre has stood strong across the ages as a meeting place for history, art, and human
            civilization, we believe that a digital platform is more than just soulless lines of code. Inspired by the
            magnificence of the Louvre's curation, we view every element, feature, and interaction on this website as a
            work of digital architecture, designed with precision.<br><br>
            We are not just building a website; we are crafting a digital museum, where every pixel is a brushstroke,
            every line of code is a sculpture, and every user interaction is a dance of creativity. Just as the Louvre's
            halls echo with the footsteps of millions of visitors, we hope that our website will resonate with users,
            inviting them to explore, discover, and connect with the rich tapestry of art and culture that we have
            meticulously curated in this digital space.
        </p>
    </section>

    <section class="advantages">

        <h2>SYSTEM HIGHLIGHTS</h2>

        <div class="adv-container">

            {{-- CARD 1 --}}
            <div class="adv-card">

                <h3>REAL-TIME ANALYTICS</h3>

                <p>
                    Live dashboard analytics powered by
                    SQL Views, relational queries,
                    and transaction aggregation.
                </p>

            </div>

            {{-- CARD 2 --}}
            <div class="adv-card">

                <h3>CURATED LOUVRE DATASET</h3>

                <p>
                    Integrated museum collections curated
                    from official Louvre datasets with
                    normalized artwork metadata.
                </p>

            </div>

            {{-- CARD 3 --}}
            <div class="adv-card">

                <h3>RELATIONAL ECOSYSTEM</h3>

                <p>
                    Connected artwork, exhibition,
                    ticket, and transaction entities
                    through relational database design.
                </p>

            </div>

            {{-- CARD 4 --}}
            <div class="adv-card">

                <h3>SMART TICKETING</h3>

                <p>
                    Dynamic ticket ecosystem featuring
                    quota tracking, automated availability,
                    and transaction management.
                </p>

            </div>

            {{-- CARD 5 --}}
            <div class="adv-card">

                <h3>AUTOMATED DATA FLOW</h3>

                <p>
                    Automated exhibition and ticket
                    status generation based on
                    real-time date logic.
                </p>

            </div>

            {{-- CARD 6 --}}
            <div class="adv-card">

                <h3>IMMERSIVE EXPERIENCE</h3>

                <p>
                    Cinematic museum-inspired interface
                    designed to create a modern digital
                    art exploration experience.
                </p>

            </div>

        </div>

    </section>

    <section class="team-section">

        <h2>OUR TEAM</h2>

        <div class="team-container">

            <div class="team-card">

                <img src="images/rizal.jpeg" alt="Rizal">

                <h3>Fahri Arizal</h3>

                <p>Full Stack Developer</p>

                <div class="socials">
                    <a href="https://www.instagram.com/ryyzlls?igsh=MWR6emVzOXl1M21xNg=="><img
                            src="{{ asset('images/instagram.png') }}"></a>

                    <a href="mailto:fahriarizal505@gmail.com"><img src="{{ asset('images/gmail.png') }}"></a>

                    <a href="https://github.com/ryyzls"><img src="{{ asset('images/github.png') }}"></a>
                </div>

            </div>

            <div class="team-card">

                <img src="images/mar.jpeg" alt="Mar">

                <h3>Mar'ie Rizqullah</h3>

                <p>Database Engineer</p>

                <div class="socials">
                    <a href="https://www.instagram.com/marrierzkl17?igsh=MWpmcTdqbWdnc2FudA=="><img
                            src="{{ asset('images/instagram.png') }}"></a>

                    <a href="mailto:marierizqullah06@gmail.com"><img src="{{ asset('images/gmail.png') }}"></a>

                    <a href="https://github.com/Mar1701"><img src="{{ asset('images/github.png') }}"></a>
                </div>

            </div>

            <div class="team-card">

                <img src="images/juda.png" alt="Juda">

                <h3>Juda Turnip</h3>

                <p>Backend Developer</p>

                <div class="socials">
                    <a href="https://www.instagram.com/juddtrnp?igsh=bnRxOGZkaHV5d2Jp"><img
                            src="{{ asset('images/instagram.png') }}"></a>

                    <a href="mailto:judabenhur3104@gmail.com"><img src="{{ asset('images/gmail.png') }}"></a>

                    <a href="https://github.com/Juda-rgb"><img src="{{ asset('images/github.png') }}"></a>
                </div>

            </div>

            <div class="team-card">

                <img src="images/azkha.jpeg" alt="Azkha">

                <h3>Azkha Amorie</h3>

                <p>Frontend Developer</p>

                <div class="socials">
                    <a href="https://www.instagram.com/16azkhaaa?igsh=aGhyMnF0aTQybGJz"><img
                            src="{{ asset('images/instagram.png') }}"></a>

                    <a href="mailto:azkhaamorie@gmail.com"><img src="{{ asset('images/gmail.png') }}"></a>

                    <a href="https://github.com/azkhaamorie"><img src="{{ asset('images/github.png') }}"></a>
                </div>

            </div>

        </div>

    </section>

    <section class="comment-section">

        <div class="comment-wrapper">

            <h2>USER COMMENTS</h2>

            <form action="/comment/store" method="POST" class="comment-form">

                @csrf

                <textarea name="message" placeholder="Add a comment..." required rows="1" id="commentInput"></textarea>

                <button type="submit">
                    Send
                </button>

            </form>

            <div class="youtube-comments">

                @foreach($comments as $comment)

                    <div class="youtube-comment">

                        <div class="profile-circle">
                            {{ strtoupper(substr($comment->name, 0, 1)) }}
                        </div>

                        <div class="comment-content">

                            <h4>{{ $comment->name }}</h4>

                            <p>{{ $comment->message }}</p>

                            <div class="comment-actions">

                                <button onclick="toggleEdit({{ $comment->id }})" class="text-btn">
                                    Edit
                                </button>

                                <form action="/comment/{{ $comment->id }}" method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="text-btn delete-text">
                                        Delete
                                    </button>

                                </form>

                            </div>

                            <div class="edit-form" id="edit-form-{{ $comment->id }}">

                                <form action="/comment/{{ $comment->id }}" method="POST">

                                    @csrf
                                    @method('PUT')

                                    <textarea name="message">{{ $comment->message }}</textarea>

                                    <button type="submit" class="save-btn">
                                        Save
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

        function toggleEdit(id) {
            const form = document.getElementById('edit-form-' + id);

            if (form.style.display === 'block') {
                form.style.display = 'none';
            }
            else {
                form.style.display = 'block';
            }
        }

    </script>

    <script>
        const textarea = document.getElementById('commentInput');

        textarea.addEventListener('input', function () {

            this.style.height = '40px';

            this.style.height = this.scrollHeight + 'px';

        });

        textarea.addEventListener('keydown', function (e) {

            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();

                this.form.submit();
            }

        });

    </script>

</body>

</html>