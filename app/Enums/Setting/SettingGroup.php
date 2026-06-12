<?php

namespace App\Enums\Setting;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

final class SettingGroup extends Enum implements LocalizedEnum
{
    const General = 1;
    const Config = 2;
    const MiniApp = 3;
    const Footer = 4;
    const Contact = 5;
    const Information = 6;
    const Slider = 7;
    const CMSTheme = 8;
    const Membership = 9;
    // const WebTheme = 10;
    const MobileTheme = 11;
}
