/*
 Copyright (c) 2007-2022, CKSource Holding sp. z o.o. All rights reserved.
 For licensing, see LICENSE.html or https://ckeditor.com/sales/license/ckfinder
 */

var config = {};

// Set your configuration options below.

// Crop mặc định luôn là hình vuông
config.imageEditOptions = {
  crop: {
    aspectRatio: 1, // Tỷ lệ 1:1 (hình vuông)
  },
};

CKFinder.define(config);
