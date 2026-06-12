<style>
    /* Custom gradient backgrounds for metric cards */
    .gradient-blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .gradient-green {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .gradient-purple {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .gradient-yellow {
        background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
    }

    .gradient-red {
        background: linear-gradient(135deg, #f857a6 0%, #ff5858 100%);
    }

    .gradient-orange {
        background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%);
    }

    .gradient-teal {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .gradient-indigo {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .gradient-pink {
        background: linear-gradient(135deg, #ff5f6d 0%, #ffc371 100%);
    }

    .gradient-gray {
        background: linear-gradient(135deg, #bdc3c7 0%, #2c3e50 100%);
    }

    /* Metric card hover effects */
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    /* Metric icon styling */
    .metric-icon {
        font-size: 3rem;
        opacity: 0.8;
    }

    /* Metric number styling */
    .metric-number {
        font-size: 1.5rem;
        font-weight: 700;
    }

    /* Metric label styling */
    .metric-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
</style>

<style>
    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .metric-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .metric-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
        border-radius: 17px;
        z-index: -1;
    }

    .metric-number {
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        position: relative;
    }

    .metric-label {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .metric-icon {
        filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    /* Activity Section */
    .activity-section {
        margin-top: 2rem;
    }

    .activity-filters {
        display: flex;
        gap: 0.5rem;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .filter-btn.active {
        background: #ff6b00;
        color: white;
        border-color: #ff6b00;
    }

    .filter-btn:hover {
        background: #f8f9fa;
    }

    .filter-btn.active:hover {
        background: #e55a00;
    }

    .activity-list {
        overflow-x: hidden;
        max-height: 400px;
        overflow-y: auto;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border: 1px solid #eee;
        border-radius: 8px;
        transition: all 0.3s ease;
        flex: 0 0 calc(50% - 0.5rem);
        box-sizing: border-box;
    }

    .activity-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }

    .activity-item.hidden {
        display: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .activity-icon.success {
        background: #d4edda;
    }

    .activity-icon.info {
        background: #cce7ff;
    }

    .activity-icon.warning {
        background: #fff3cd;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .activity-desc {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .activity-time {
        color: #999;
        font-size: 0.8rem;
    }
</style>
