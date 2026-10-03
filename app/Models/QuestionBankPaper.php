<?php

namespace App\Models;

use App\Enums\CompetitionLevel;
use App\Enums\CompetitionModuleType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QuestionBankPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'year',
        'level',
        'module_type',
        'file_path',
        'file_name',
        'file_size',
        'uploaded_by',
        'download_count',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'level' => CompetitionLevel::class,
            'module_type' => CompetitionModuleType::class,
            'file_size' => 'integer',
            'download_count' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function formattedSize(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->file_size >= 1048576) {
                    return number_format($this->file_size / 1048576, 2) . ' MB';
                }
                return number_format($this->file_size / 1024, 1) . ' KB';
            }
        );
    }

    public static function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
