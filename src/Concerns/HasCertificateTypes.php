<?php

namespace HusamTariq\FilamentCertificateGenerator\Concerns;

interface HasCertificateTypes
{
    /**
     * Get display text for a certificate type
     *
     * @param string $typeKey The type identifier
     * @param bool $forSelect Whether to format for select options
     * @return string|array Display text or [key => value] pair
     */
    public function getTypeDisplay(string $typeKey, bool $forSelect = false): string|array;

    /**
     * Get all available certificate type options
     *
     * @return array Array of type options [key => display]
     */
    public function getTypeOptions(): array;

    /**
     * Get the badge color for a certificate type
     *
     * @param string $typeKey The type identifier
     * @return string Color class or value
     */
    public function getTypeColor(string $typeKey): string;

    /**
     * Get the icon for a certificate type
     *
     * @param string $typeKey The type identifier
     * @return string|null Icon identifier or null
     */
    public function getTypeIcon(string $typeKey): ?string;

    /**
     * Get all configured certificate types
     *
     * @return array All certificate type configurations
     */
    public function getCertificateTypes(): array;
}
