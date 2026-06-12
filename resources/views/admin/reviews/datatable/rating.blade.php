<div class="review_rating">
    @for ($i = 1; $i <= $rating; $i++)
        <span class="star" style="color:orange">★</span>
    @endfor
    @for ($i = 5; $i > $rating; $i--)
        <span class="star" style="color: gray">★</span>
    @endfor
</div>
