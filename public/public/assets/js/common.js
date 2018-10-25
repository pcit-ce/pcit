function formatGitType(gittype) {
  switch (gittype) {
    case 'github':
      return 'GitHub';

    default:
      return gittype.substring(0, 1).toUpperCase() + gittype.substring(1);
  }
}
