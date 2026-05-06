<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/storage.php';

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

class RIP_Binary_Command {

    public function learn($args, $assoc_args) {
        $route = $assoc_args['route'] ?? null;
        $url   = $assoc_args['url'] ?? null;

        if (!$route || !$url) {
            WP_CLI::error('Usage: wp rip learn --route=/wp-json/wc/v3/products --url=https://example.com/wp-json/wc/v3/products');
        }

        $plugins = get_option('active_plugins', []);
        if (!is_array($plugins) || empty($plugins)) {
            WP_CLI::error('No active plugins found.');
        }

        WP_CLI::log('RIP Binary Mode started');
        WP_CLI::log('Route: ' . $route);
        WP_CLI::log('URL: ' . $url);
        WP_CLI::log('Testing active plugins: ' . count($plugins));

        $baseline = $this->fetch($url, []);
        if (!$baseline) {
            WP_CLI::error('Could not fetch baseline response. Check URL/authentication.');
        }

        $safe = $this->binary_find_safe($url, $baseline, $plugins);

        if (!rip_save_profile($route, $safe)) {
            WP_CLI::error('Could not save profile to ' . rip_profiles_file());
        }

        WP_CLI::success('Saved learned profile to ' . rip_profiles_file());
        WP_CLI::success('Safe plugins to disable: ' . count($safe));

        foreach ($safe as $plugin) {
            WP_CLI::log('SAFE: ' . $plugin);
        }
    }

    private function binary_find_safe(string $url, string $baseline, array $plugins): array {
        $safe = [];
        $queue = [array_values($plugins)];

        while (!empty($queue)) {
            $group = array_shift($queue);
            if (empty($group)) {
                continue;
            }

            WP_CLI::log('Testing group size: ' . count($group));
            $response = $this->fetch($url, $group);

            if ($this->same($baseline, $response)) {
                WP_CLI::log('Group is safe: ' . count($group));
                $safe = array_merge($safe, $group);
                continue;
            }

            if (count($group) === 1) {
                WP_CLI::warning('Keep loaded: ' . $group[0]);
                continue;
            }

            $half = (int) ceil(count($group) / 2);
            foreach (array_chunk($group, $half) as $chunk) {
                $queue[] = $chunk;
            }
        }

        return array_values(array_unique($safe));
    }

    private function fetch(string $url, array $disabled_plugins): ?string {
        $response = wp_remote_get($url, [
            'timeout' => 45,
            'sslverify' => false,
            'headers' => [
                'X-RIP-Test' => '1',
                'X-RIP-Disabled' => base64_encode(wp_json_encode($disabled_plugins)),
                'Cache-Control' => 'no-cache',
            ],
        ]);

        if (is_wp_error($response)) {
            WP_CLI::warning($response->get_error_message());
            return null;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        if ($code < 200 || $code >= 300) {
            WP_CLI::warning('HTTP ' . $code . ' from test request');
            return null;
        }

        return $this->normalize_json(wp_remote_retrieve_body($response));
    }

    private function normalize_json(string $body): string {
        $data = json_decode($body, true);
        if (!is_array($data)) {
            return trim($body);
        }

        $this->remove_unstable_keys($data);
        $this->recursive_sort($data);

        return wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function remove_unstable_keys(&$data): void {
        if (!is_array($data)) {
            return;
        }

        $unstable = ['date_modified', 'date_modified_gmt', 'generated_at', 'timestamp'];
        foreach ($unstable as $key) {
            if (array_key_exists($key, $data)) {
                unset($data[$key]);
            }
        }

        foreach ($data as &$value) {
            $this->remove_unstable_keys($value);
        }
    }

    private function recursive_sort(&$array): void {
        if (!is_array($array)) {
            return;
        }

        foreach ($array as &$value) {
            $this->recursive_sort($value);
        }

        if ($this->is_assoc($array)) {
            ksort($array);
        }
    }

    private function is_assoc(array $array): bool {
        if ($array === []) {
            return false;
        }
        return array_keys($array) !== range(0, count($array) - 1);
    }

    private function same(?string $a, ?string $b): bool {
        return is_string($a) && is_string($b) && hash_equals(md5($a), md5($b));
    }
}

WP_CLI::add_command('rip', 'RIP_Binary_Command');
