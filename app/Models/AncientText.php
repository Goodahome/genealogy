<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AncientText extends Model
{
    protected $fillable = ['team_id', 'title', 'content', 'volume', 'page', 'show_on_homepage'];

    protected $casts = [
        'show_on_homepage' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    private function numberToChinese($number)
    {
        $chineseNumbers = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
        return $chineseNumbers[$number] ?? $number;
    }

    public function calculateTotalPages($linesPerPage = 17)
    {
        $lines = $this->splitContentIntoLines($this->content);
        $paginated = array_chunk($lines, $linesPerPage);
        return count($paginated);
    }

    public function getPaginatedContent($linesPerPage = 17)
    {
        $lines = $this->splitContentIntoLines($this->content);
        $paginated = array_chunk($lines, $linesPerPage);

        $volumeChinese = $this->numberToChinese($this->volume);
        foreach ($paginated as $pageIndex => &$pageContent) {
            // 确保每页有 17 行，不足时填充空行
            $currentLineCount = count($pageContent);
            if ($currentLineCount < $linesPerPage) {
                for ($i = $currentLineCount; $i < $linesPerPage; $i++) {
                    $pageContent[$i] = [
                        'content' => '',
                        'isFirstLineOfParagraph' => false
                    ];
                }
            }

            // 固定将 footer 添加到第 17 行（索引 16）
            $pageContent[$linesPerPage - 1]['footer'] = "第{$volumeChinese}卷第" . $this->numberToChinese($pageIndex + 1) . "页";
        }

        return $paginated;
    }

    private function splitContentIntoLines($content)
    {
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        $paragraphs = explode("\n", $content);
        $finalLines = [];
        
        $linesPerPage = 17;
        $maxCharsPerLine = 27;

        foreach ($paragraphs as $paragraph) {
            if (trim($paragraph) === '') continue;
            $paragraph = trim($paragraph);
            
            $currentLine = '';
            $paragraphLines = [];
            $isFirstLine = true;

            $chars = preg_split('//u', $paragraph, -1, PREG_SPLIT_NO_EMPTY);
            $i = 0;

            while ($i < count($chars)) {
                $remainingSpace = $isFirstLine ? $maxCharsPerLine - 2 : $maxCharsPerLine;

                while ($i < count($chars) && mb_strlen($currentLine) < $remainingSpace) {
                    $currentLine .= $chars[$i];
                    $i++;
                }

                $currentLine = trim($currentLine);
                if ($currentLine === '') {
                    $currentLine = '';
                    continue;
                }

                $paragraphLines[] = $currentLine;
                $currentLine = '';
                $isFirstLine = false;
            }

            foreach ($paragraphLines as $lineIndex => $line) {
                $finalLines[] = [
                    'content' => $line,
                    'isFirstLineOfParagraph' => $lineIndex === 0
                ];
            }
        }

        // 确保字符分配正确，避免第 17 行超长
        $adjustedLines = [];
        $currentPageLines = [];
        foreach ($finalLines as $line) {
            $currentPageLines[] = $line;
            if (count($currentPageLines) == $linesPerPage) {
                $adjustedLines[] = $currentPageLines;
                $currentPageLines = [];
            }
        }
        if (!empty($currentPageLines)) {
            $adjustedLines[] = $currentPageLines;
        }

        // 将调整后的行重新分配到 finalLines
        $finalLines = [];
        foreach ($adjustedLines as $page) {
            foreach ($page as $line) {
                $finalLines[] = $line;
            }
        }

        return $finalLines;
    }
}