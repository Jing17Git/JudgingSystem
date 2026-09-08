<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Top Qualifiers Count
    |--------------------------------------------------------------------------
    |
    | The number of top candidates from each division (Male / Female) that
    | advance from the Preliminary stage to the Q&A Final stage.
    | Default is 5 (Top 5 Male + Top 5 Female = 10 finalists).
    |
    */
    'top_qualifiers_count' => (int) env('PAGEANT_TOP_QUALIFIERS_COUNT', 5),
];
