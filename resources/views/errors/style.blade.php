<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Theme colors for each error */
    :root {
        --error-400: linear-gradient(135deg, #ff7e00 0%, #ffae42 100%);
        --error-401: linear-gradient(135deg, #b71c1c 0%, #f44336 100%);
        --error-403: linear-gradient(135deg, #6a1b9a 0%, #ab47bc 100%);
        --error-404: linear-gradient(135deg, #1976d2 0%, #64b5f6 100%);
        --error-419: linear-gradient(135deg, #673ab7 0%, #9575cd 100%);
        --error-429: linear-gradient(135deg, #ffeb3b 0%, #fbc02d 100%);
        --error-500: linear-gradient(135deg, #e65100 0%, #ff7043 100%);
        --error-503: linear-gradient(135deg, #424242 0%, #9e9e9e 100%);
    }

    body {
        font-family: 'Arial', sans-serif;
        background: var(--error-color, linear-gradient(135deg, #1a1a2e 0%, #16213e 100%));
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .title {
        font-size: 5rem;
        font-weight: bold;
        background: var(--error-color, #fff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 2px 2px 8px rgba(255, 255, 255, 0.8),
            4px 4px 12px rgba(0, 0, 0, 0.6);
        /* Hiệu ứng phát sáng và bóng */
        margin-bottom: 1rem;
        text-stroke: 2px rgba(255, 255, 255, 0.8);
        /* Viền chữ giúp dễ đọc hơn */
        -webkit-text-stroke: 2px rgba(255, 255, 255, 0.8);
    }

    .button {
        display: inline-block;
        padding: 1rem 2rem;
        background: var(--error-color, #6b3de0);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: bold;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .button:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.4);
    }

    .stars {
        position: absolute;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .star {
        position: absolute;
        background: white;
        border-radius: 50%;
        animation: twinkle 1s infinite;
    }

    @keyframes twinkle {

        0%,
        100% {
            opacity: 0.3;
        }

        50% {
            opacity: 1;
        }
    }

    .container {
        text-align: center;
        padding: 2rem;
        max-width: 600px;
        z-index: 1;
    }

    @keyframes shine {
        to {
            background-position: 200% center;
        }
    }

    .subtitle {
        color: #fff;
        font-size: 1.5rem;
        margin-bottom: 2rem;
        opacity: 0.8;
    }

    .message {
        color: #ccc;
        font-size: 1rem;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .title {
            font-size: 5rem;
        }

        .subtitle {
            font-size: 1.2rem;
        }

        .message {
            font-size: 0.9rem;
        }

        .button {
            padding: 0.8rem 1.6rem;
        }
    }

    @media (max-width: 480px) {
        .title {
            font-size: 3rem;
        }

        .container {
            padding: 1rem;
        }
    }
</style>