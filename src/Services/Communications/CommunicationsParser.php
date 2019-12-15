<?php


namespace App\Services\Communications;


class CommunicationsParser
{

    const PHONE_CALL_IDENTIFIER = 'C';
    const SMS_IDENTIFIER = 'S';

    public function __invoke(string $data): array
    {
        $communications = [];
        $lines = $this->parseFile($data);
        foreach ($lines as $line) {
            $communication = $this->parseLine($line);
            if (isset($communication)) {
                $contact = $communication['incoming'] ? $communication['source'] : $communication['destination'];
                $communications[$contact][] = $communication;
            }
        }
        return $communications;
    }

    private function parseFile(string $data): array
    {
        return explode(PHP_EOL, $data);
    }

    private function parseLine(string $line): ?array
    {
        if (strlen($line) <= 0) {
            return null;
        }

        $type = $line[0];
        $info = substr($line, 1);
        if ($type === self::PHONE_CALL_IDENTIFIER) {
            return $this->parsePhoneCall($info);
        } else if ($type === self::SMS_IDENTIFIER) {
            return $this->parseSMS($info);
        }
        return null;
    }

    private function parsePhoneCall(string $info): array
    {
        return  [
            'type' => self::PHONE_CALL_IDENTIFIER,
            'source' => trim(substr($info, 0, 9)),
            'destination' => trim(substr($info, 9, 9)),
            'incoming' => (bool) substr($info, 18, 1),
            'name' => trim(substr($info, 19, 24)),
            'date' => $this->parseDate(substr($info, 43, 14)),
            'duration' => $this->parseDuration((int) substr($info, 57))
        ];
    }

    private function parseSMS(string $info): array
    {
        return  [
            'type' => self::SMS_IDENTIFIER,
            'source' => trim(substr($info, 0, 9)),
            'destination' => trim(substr($info, 9, 9)),
            'incoming' => (bool) substr($info, 18, 1),
            'name' => trim(substr($info, 19, 24)),
            'date' => $this->parseDate(substr($info, 43))
        ];
    }

    private function parseDate(string $date): ?\DateTime
    {
        return \DateTime::createFromFormat('dmYHis', $date)?:null;
    }

    private function parseDuration(int $seconds): string
    {
        $hours = floor($seconds/3600);
        $minutes = floor(($seconds%3600)/60);
        $secs = (($seconds%3600)%60);
        return $hours.':'.$minutes.':'.$secs;
    }
}