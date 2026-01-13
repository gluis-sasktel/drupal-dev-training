
/** Renovate configuration for GitHub repositories
 * Place this file at: ./renovate/config.js
 * Requires GitHub Actions secrets:
 *  - RENOVATE_TOKEN                   (GitHub PAT used by Renovate to access this repo)
 *  - RENOVATE_GITHUB_COM_TOKEN (opt.) (GitHub PAT used to fetch GitHub changelogs/releases)
 */

module.exports = {
  // Core settings
  platform: 'github',
  onboarding: true,                 // Creates a PR with onboarding config if none exists
  requireConfig: false,             // Allow Renovate to run with this shared config
  username: 'renovate[bot]',        // If you use a custom account, set that username here
  gitAuthor: 'Renovate Bot <bot@renovateapp.com>',

  // Authentication (tokens come from env; set via GitHub Actions secrets)
  // RENOVATE_TOKEN is injected by the runner; no need to hardcode here.
  // Optional: token for fetching GitHub changelogs to avoid rate limiting
  hostRules: [
    {
      hostType: 'github',
      matchHost: 'github.com',
      token: process.env.RENOVATE_GITHUB_COM_TOKEN, // optional
    },
  ],

  // Scheduling (reduce noise to business hours in your timezone)
  timezone: 'America/Regina',
  schedule: [
    'after 08:00 and before 17:00 on Monday,Tuesday,Wednesday,Thursday,Friday',
  ],

  // Branch & commit behavior
  semanticCommits: 'enabled',
  commitMessagePrefix: 'chore(deps): ',
  rangeStrategy: 'replace',         // Update to latest allowed version; use 'widen' if you prefer

  // PR management
  prConcurrentLimit: 5,
  prHourlyLimit: 2,
  labels: ['dependencies', 'renovate'],
  reviewers: [],                    // e.g., ['GerardoLuisMartinez'] if you want auto-reviewers
  assignees: [],

  // Automerge policy (safe defaults)
  automerge: false,                 // Start disabled; enable per-package or globally when comfortable
  // Example per-package automerge for patch/minor:
  packageRules: [
    {
      matchUpdateTypes: ['patch'],
      automerge: true,
      platformAutomerge: true,
      labels: ['renovate:automerge'],
    },
    {
      matchUpdateTypes: ['minor'],
      automerge: false, // flip to true when ready
    },
    // Group common ecosystem updates
    {
      matchManagers: ['npm', 'pnpm', 'yarn'],
      groupName: 'JavaScript dependencies',
    },
    {
      matchManagers: ['composer'],
      groupName: 'PHP/Composer dependencies',
    },
    {
      matchManagers: ['dockerfile'],
      groupName: 'Docker base images',
    },
    // Drupal/Composer specifics
    {
      matchManagers: ['composer'],
      matchDepTypes: ['require', 'require-dev'],
      rangeStrategy: 'replace',
      // If you want to restrict major upgrades:
      allowedVersions: '<=99', // adjust or remove as needed
    },
  ],

  // Lock file maintenance
  lockFileMaintenance: {
    enabled: true,
    schedule: ['before 06:00 on Monday'], // weekly
    branchTopic: 'lock-file',
  },

  // Dependency dashboard (issue with checkboxes to control Renovate)
  dependencyDashboard: true,
  dependencyDashboardApproval: false,

  // Respect GitHub’s status checks
  requiredStatusChecks: null, // set to [] to ignore, or to array of required checks if you enforce them

  // Opt-in managers (enable/disable depending on your repo)
  enabledManagers: [
    'composer',     // Drupal/PHP
    'npm',          // if you have front-end JS
    'dockerfile',   // if you have Dockerfiles
    'github-actions',
  ],

  // Stability: ignore unstable releases if desired
  ignoreUnstable: false, // set true to avoid pre-releases

  // Repository-specific constraints
  // Set your base branch if not 'main'
  baseBranches: ['main'],
};
